$(window).load (function () {
	// Inicializa WebGL usando webgl.js
	render.begin("canvas");
	render.enableBlending();

	render.makeShader ("gradient", "v-gradient", "f-gradient",
	{vertexPosition: "vertexPosition"},
	{pMatrix: "pMatrix",
	 mvMatrix: "mvMatrix",
	 nMatrix: "nMatrix",
	 color: "color"});

	// Inicializa teclado e mouse
	html5.canvas = render.canvas;
	html5.init();
	html5.enableInput();

	// Carrega modelo da esfera
	render.loadModel("esfera.js");

	// Cria um modelo para uma linha.
	render.createBuffer("linha.vert", new Float32Array([
		0, 0, 0,
		0, 0, 0
	]), 3, false);

	render.createBuffer("linha.obj", new Uint16Array([
		0, 1
	]), 1, true);

	// Cria um modelo para um retângulo
	render.createBuffer("retangulo.vert", new Float32Array([
		-1, -1, 0,
		1, -1, 0,
		1, 1, 0,
		-1, 1, 0
	]), 3, false);

	render.createBuffer("retangulo.obj", new Uint16Array([
		0, 1, 2,
		0, 3, 2
	]), 1, true);

	// Cor de fundo
	render.clearColor([0,0,0,1]);

	// Prepara o viewport (automático)
	render.viewport();

	// Está tudo ok?
	if (render.good()) {
	// Começa a desenhar a cena
	setTimeout (draw, 0);
	} else {
		alert ("Erro ao preparar WebGL!");
	}

	resizeInterface();
	$(window).resize(function () {
		setTimeout(resizeInterface, 0);
		setTimeout(resizeInterface, 0);
	});

	if (typeof istoriaLoadInfoID != 'undefined') {
			net.loadInfo(istoriaLoadInfoID);
	}

	window.scrollTo(0, 1);
});

function Info (p, v) {
	this.color = [Math.random()/2+0.2,Math.random()/2+0.2,Math.random()/2+0.2,0.5];
	this.p = [0,0,0];
	if (p !== undefined) {
		this.p = vec3.clone(p);
	}
	this.v = [Math.random()-Math.random(), Math.random()-Math.random(), Math.random()-Math.random()];
	if (v !== undefined) {
		this.v = vec3.clone(v);
		vec3.normalize(this.v, this.v);
		vec3.add(this.v, this.v, [(Math.random()-Math.random()), (Math.random()-Math.random()), (Math.random()-Math.random())]);
		vec3.scale(this.v, this.v, 0.2);
	}
	this.related = [];
	this.selected = false;

	this.name = '';
	this.id = 0;
	this.content = '';

	this.addRelation = function (r) {
		if (this.related.indexOf(r) < 0) {
			this.related.push(r);
		}
	}

	this.relatedTo = function (r) {
		return (this.related.indexOf(r) >= 0);
	}

	this.step = function () {
		this.p[0] += this.v[0];
		this.p[1] += this.v[1];
		this.p[2] += this.v[2];

		this.v[0] *= 0.96;
		this.v[1] *= 0.96;
		this.v[2] *= 0.96;
	}

	this.draw = function (original) {
		render.useBuffer("esfera.js.vert","vertexPosition");
		render.useBuffer("esfera.js.obj", null);
		render.gl.uniform4fv(render.activeShader.uUniforms.color,
				new Float32Array (this.color))
		mat4.translate(render.mvMatrix, original, this.p);

		if (this.selected) {
			mat4.scale(render.mvMatrix, render.mvMatrix, [2, 2, 2]);
			this.color[3] = 1;
		} else {
			this.color[3] = 0.2;
			if (cam.selection) {
				if (this.relatedTo(cam.selection)) {
							this.color[3] = 1;
				}
			}
		}

		// Mostra nome se estiver em "highlight"
		if (this.color[3] == 1) {
			if (!this.element) {
				this.element = $("<div class='nome'></div>");
				this.element.text(this.name);
				$(".ist-visualizacao").append(this.element);
				this.element.fadeIn();
			}

			var pos = net.coord(original, this.p);

			var right = document.getElementById("canvas").style.left;
			right = right.substring(0, right.length-2);

			if (right.length == 0)
				right = '0';

			pos[0] += Math.floor(right);

			pos[0] += 24;
			pos[1] -= this.element.height()-4;

			this.element.css("left", pos[0]+"px");
			this.element.css("top", pos[1]+"px");
		} else if (this.element) {
			this.element.remove();
			this.element = null;
		}

		render.draw(false);
	}
}

function Camera () {
	this.distance = 50;
	this.eye = [0, 0, 0];
	this.vec = [0, 0, this.distance];
	this.baseVec = [0, 0, this.distance];
	this.baseUp = [0, 1, 0];
	this.up = [0,1,0];
	this.position = [0, 0, 0];
	this.rotation = [1, 1, 1];
	this.baseRotation = [1, 1, 1];
	this.v = 0;
	this.d = 30;
	this.selection = null;
	this.t = 0;
	this.play = false;
	this.clickTime = 0;

	this.rotate = function (v) {
		var r = mat4.create();

		// Rotaciona pelos ângulos inversos (mvMatrix contem a rotação dos objetos, aqui queremos a da câmera)
		mat4.rotate(r, r, Math.PI*2-this.rotation[0], [1,0,0]);
		mat4.rotate(r, r, Math.PI*2-this.rotation[1], [0,1,0]);
		mat4.rotate(r, r, Math.PI*2-this.rotation[2], [0,0,1]);

		vec3.transformMat4(v, v, r);

		return v;
	}

	// o = Origem da linha
	// n = Direção da linha (normalizada)
	// c = Centro da esfera
	this.distanceRaySphere = function (o, n, c) {
		// Cria buffers para os cálculos
		var oc = vec3.create();
		var dt = vec3.create();
		var pr = vec3.create();
		var p = vec3.create();
		var r = vec3.create();

		// Projeção do centro da esfera sobre a linha
		vec3.sub(oc, c, o);
		vec3.scale(pr, n, vec3.dot(oc, n));
		vec3.add(p, pr, o);

		vec3.sub(r, p, c);

		return vec3.len(r);
	}

	this.intersectRaySphere = function (o, n, c, r) {
		// Line equation: p = o+t*n

		var oc = vec3.create();

		vec3.sub(oc, o, c);

		// Finding delta
		var delta = vec3.dot(n, oc)*vec3.dot(n, oc)-vec3.sqrLen(n)*(vec3.sqrLen(oc)-r*r);
		var t, t2=false;

		if (delta < 0) {
			return false;
		}

		var b = -vec3.dot(n, oc);

		// Finding t
		t = (b+Math.sqrt(delta))/vec3.len(n);

		if (delta > 0) {
			t2 = (b-Math.sqrt(delta))/vec3.len(n);
		}

		// Finding p
		var p2 = false;
		var p = vec3.create();
		vec3.scale(p, n, t);
		vec3.add(p, p, o);

		if (t2) {
			p2 = vec3.create();
			vec3.scale(p2, n, t2);
			vec3.add(p2, p2, o);

			var dist1 = vec3.create();
			var dist2 = vec3.create();

			vec3.sub(dist1, p, o);
			vec3.sub(dist2, p2, o);

			if (vec3.len(dist1) > vec3.len(dist2)) {
				return p2;
			} else {
				return p;
			}
		}

		return p;
	}

	this.select = function (i, double, noload) {
		var inf = i;
		if (this.selection)
			this.selection.selected = false;
		this.selection = inf;
		this.selection.selected = true;

		if (!noload)
			net.loadInfo(this.selection.id);

		if (double || sidebar.isOpen()) {
			sidebar.open('show', this.selection.id);
		}
	}

	this.approximatte = function (v) {
		v[0] = Math.floor(v[0]*100)/100;
		v[1] = Math.floor(v[1]*100)/100;
		v[2] = Math.floor(v[2]*100)/100;
	}

	this.step = function () {
		if (this.play)
			this.t += 0.01;

		if (this.selection) {
			vec3.sub(this.position, this.selection.p, [0,0,0]);
		}

		var wheel = html5.mouseWheel;

		if (html5.mouseWheel > 0) {
			if (vec3.len(this.vec) < 350) {
				vec3.scale(this.vec, this.vec, 1+(html5.mouseWheel/1000));
				vec3.scale(this.baseVec, this.baseVec, 1+(html5.mouseWheel/1000));
			}
		}

		if (html5.mouseWheel < 0) {
			if (vec3.len(this.vec) > 10) {
				vec3.scale(this.vec, this.vec, 1+(html5.mouseWheel/1000));
				vec3.scale(this.baseVec, this.baseVec, 1+(html5.mouseWheel/1000));
			}
		}

		html5.mouseWheel -= wheel;

		var distance = vec3.len(this.vec);

		if (this.vec) {
			vec3.add(this.eye, this.position, this.vec);
		}

		mat4.identity(render.mvMatrix);
		mat4.lookAt(render.mvMatrix, this.eye, this.position, this.up);

		var world = net.icoord(render.mvMatrix, [html5.mousePos[0], html5.mousePos[1], 0]);

		var origin = vec3.clone([0,0,0]);
		var inv = mat4.create();
		mat4.invert(inv, render.mvMatrix);
		vec3.transformMat4(origin, origin, inv);

		var d = vec3.create();
		vec3.sub(d, world, origin);
		vec3.normalize(d, d);

		var min=Number.MAX_VALUE;
		var mini=null;
		for (var i in net.infos) {
			var r;
			var toCam = vec3.create();
			vec3.sub(toCam, net.infos[i].p, origin);
			if ((r=vec3.len(toCam) < min && this.distanceRaySphere(origin, d, net.infos[i].p) < 1)) {
				min = r;
				mini = net.infos[i];
			}
		}

		if (mini) {
			$("#canvas").css("cursor", "pointer");
		} else if (this.selection) {
			if ($("#canvas").css("cursor") != 'grabbing')
				$("#canvas").css("cursor", "default");
		}

		if (html5.mouseButtonClick) {
			$("#canvas").css("cursor", "-webkit-grabbing");
			$("#canvas").css("cursor", "-moz-grabbing");
			$("#canvas").css("cursor", "grabbing");

			var worldDelta = net.icoord(render.mvMatrix, [html5.mouseClickPos[0], html5.mouseClickPos[1], 0]);
			var dStart = vec3.create();
			vec3.sub(dStart, worldDelta, origin);
			vec3.normalize(dStart, dStart);

			var ep = this.intersectRaySphere(origin, d, this.position, distance*0.99);
			var sp = this.intersectRaySphere(origin, dStart, this.position, distance*0.99);

			if (ep && sp) {
				var axis = vec3.create();
				var dist = vec3.create();

				vec3.sub(ep, ep, this.position);
				vec3.sub(sp, sp, this.position);

				vec3.cross(axis, ep, sp);
				vec3.normalize(ep, ep);
				vec3.normalize(sp, sp);
				vec3.normalize(axis, axis);

				vec3.sub (dist, ep, sp);

				var angle = vec3.len(dist)*distance*4*Math.PI;

				var rot = quat.fromValues(axis[0]*Math.sin(angle/2), axis[1]*Math.sin(angle/2), axis[2]*Math.sin(angle/2), Math.cos(angle/2));
				var inv = quat.create();
				quat.normalize(rot, rot);
				quat.conjugate(inv, rot);
				var vec = vec3.clone(this.baseVec);
				var up = vec3.clone(this.baseUp);
				vec3.transformQuat(this.vec, vec, rot);
				vec3.transformQuat(this.up, up, rot);
			}

			html5.mouseClickPos[0] = html5.mousePos[0];
			html5.mouseClickPos[1] = html5.mousePos[1];
		}

		this.baseRotation = vec3.clone(this.rotation);
		this.baseVec = vec3.clone(this.vec);
		this.baseUp = vec3.clone(this.up);

		if (html5.mouseButton) {
			html5.mouseButton = false;

			var double = false;

			if (new Date()/1000-this.clickTime < 0.5) {
				double = true;
				this.clickTime = new Date()/1000-0.5;
			} else {
				this.clickTime = new Date()/1000;
			}

			if (mini)
				this.select(mini, double);
		}
	}
}

function Network () {
	this.infos = [];

	var n = 0;

	for (var i=0;i<n;i++) {
		this.infos.push(new Info());
	}

	for (var i=0;i<n;i++) {
		// Número de relacionados
		var rn = Math.floor(Math.random()*4);
		for (var j=0;j<rn;j++) {
			var k = Math.floor(Math.random()*n);
			if (i != 1) {
				this.infos[i].addRelation(this.infos[k]);
				this.infos[k].addRelation(this.infos[i]);
			}
		}
	}

	// Joga fora todos os infos e carrega apenas o represantado
	// por 'id'
	this.loadOnlyInfo = function (id) {
		this.infos = [];
		this.loadInfo(id, true);
	}

	// Pede ao servidor um info
	this.loadInfo = function (id, focus) {
		var _this = this;
		var mem = null;
		var onMemory = false;
		for (var i in this.infos) {
			if (this.infos[i].id == id) {
				mem = this.infos[i];
				onMemory = true;
			}
		}

		$.ajax({url: '../json/info/get.php?id='+id}).done(function (data) {
			var jdata = JSON.parse(data);

			// Não ocorreu nenhum erro
			if (jdata.error == 0) {
				if (!mem)
					mem = new Info();

				mem.name = jdata.message.name;
				mem.content = jdata.message.content;
				mem.id = jdata.message.id;

				if (!onMemory)
					_this.infos.push(mem);

				cam.select(mem,false,true);

				if (focus) {
					cam.select(mem);
				}

				$.ajax({url: '../json/info/related.php?id='+id}).done(function (data) {
					var jdata = JSON.parse(data);

					// Não ocorreu nenhum erro
					if (jdata.error == 0) {
						mem.related = [];

						for (var i in jdata.message) {
							var mem2 = null;
							var onMemory2 = false;
							for (var j in _this.infos) {
								if (_this.infos[j].id == jdata.message[i].id) {
									mem2 = _this.infos[j];
									onMemory2 = true;
								}
							}

							if (!mem2)
								mem2 = new Info(mem.p, mem.v);

							mem2.name = jdata.message[i].name;
							mem2.content = jdata.message[i].content;
							mem2.id = jdata.message[i].id;

							if (!onMemory2)
								_this.infos.push(mem2);

							var rm = mem2.related.indexOf(mem);

							if (rm > 0) {
								mem2.related.splice(rm, 1);
							}

							mem.addRelation(mem2);
							mem2.addRelation(mem);
						}
					}
				});
			}
		});
	}

	this.step = function () {
		if (this.infos.length == 0) {
			$(".nada-carregado").fadeIn();
		} else {
			$(".nada-carregado").fadeOut();
		}

		var v = 0.0001;
		var va = 0.0001;
		var d = 64;
		var da = 32;

		for (var i in this.infos) {
			for (var j in this.infos) {
				if (i != j) {
					var dx = (this.infos[i].p[0]-this.infos[j].p[0]);
					var dy = (this.infos[i].p[1]-this.infos[j].p[1]);
					var dz = (this.infos[i].p[2]-this.infos[j].p[2]);

					var distancesq = dx*dx+dy*dy+dz*dz;

					if (this.infos[i].relatedTo(this.infos[j]) ||
							this.infos[j].relatedTo(this.infos[i])) {
						if (distancesq > d) {
							this.infos[i].v[0] += -dx*va;
							this.infos[i].v[1] += -dy*va;
							this.infos[i].v[2] += -dz*va;
						} else if (distancesq < da) {
							this.infos[i].v[0] += dx*v;
							this.infos[i].v[1] += dy*v;
							this.infos[i].v[2] += dz*v;
						}
					} else {
						if (distancesq > 0 && distancesq < da*8) {
							this.infos[i].v[0] += dx*v*10/distancesq;
							this.infos[i].v[1] += dy*v*10/distancesq;
							this.infos[i].v[2] += dz*v*10/distancesq;
						}
					}
				}
			}
			this.infos[i].step();
		}
	}

	this.vec3to4 = function (v) {
		return [v[0], v[1], v[2], 1];
	}

	this.coord = function (original,p) {
		var cp = vec4.create();
		var mvpMatrix = mat4.create();
		mat4.mul(mvpMatrix, render.pMatrix, original);
		vec4.transformMat4(cp, vec4.clone(this.vec3to4(p)), mvpMatrix);
		cp[0] /= cp[3];
		cp[1] /= cp[3];
		return [(cp[0]+1)/2*render.width(),(1-cp[1])/2*render.height()];
	}

	this.icoord = function (original,p) {
		p[0] = 2*p[0]/render.width()-1;
		p[1] = 1-2*p[1]/render.height();

		var cp = vec4.create();
		var mvpMatrix = mat4.create();
		var mvpiMatrix = mat4.create();
		mat4.mul(mvpMatrix, render.pMatrix, original);
		mat4.invert(mvpiMatrix, mvpMatrix);
		vec4.transformMat4(cp, vec4.clone(this.vec3to4(p)), mvpiMatrix);
		cp[0] /= cp[3];
		cp[1] /= cp[3];
		cp[2] /= cp[3];
		return [cp[0], cp[1], cp[2]];
	}

	this.drawLine = function (original, p1, p2, a) {
		render.updateBuffer("linha.vert", new Float32Array([
			p1[0], p1[1], p1[2],
			p2[0], p2[1], p2[2]
		]));
		render.useBuffer("linha.vert","vertexPosition");
		render.useBuffer("linha.obj", null);
		render.gl.uniform4fv(render.activeShader.uUniforms.color,
				new Float32Array ([1,1,1,a]))
		mat4.copy(render.mvMatrix, original);
		render.draw(true);
	}

	this.draw = function (original, nolines) {
		for (var i in this.infos) {
			this.infos[i].draw(original);
		}
		if (!nolines)
			for (var i in this.infos) {
				for (var j in this.infos[i].related) {
					var a = 0.2;
					if (cam.selection) {
						if (cam.selection == this.infos[i].related[j] || cam.selection == this.infos[i]) {
							a = 1;
						}
					}
					this.drawLine(original, this.infos[i].p, this.infos[i].related[j].p, a);
				}
			}
	}
}

var net = new Network();
var cam = new Camera();

function drawScreen () {
	render.useBuffer("retangulo.vert","vertexPosition");
	render.useBuffer("retangulo.obj", null);
	render.draw(false);
}

// Desenha com WebGL
function draw () {
	render.useFrameBuffer("default");

	render.viewport();
	render.clear();

	// Gradiente de fundo
	render.useShader("gradient");
	drawScreen();

	render.clearDepth();

	render.useShader("default");

	mat4.perspective (render.pMatrix,
			3.14/4, render.width()/render.height(),
			1, 400);

	// Coloca a camera em posição
	cam.step();
	net.step();
	var original = mat4.clone(render.mvMatrix);
	net.draw(original);

	if (typeof stopall === 'undefined')
		setTimeout (draw, 1000/60);
}

function resizeInterface () {
	var width = $(window).width();
	var height = $(window).height()-$(".ist-barra").height();

	$("#canvas").css("width",width+"px");
	$("#canvas").css("height",height+"px");
	render.canvas.width = width;
	render.canvas.height = height;

	render.viewport();
}

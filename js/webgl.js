function preventEvent (evt) {
	evt.preventDefault();
}

models = {}

render = new function () {
	this.gl = null;

	this.shaders = [];
	this.buffers = [];
	this.framebuffers = {
		"default": null
	};

	this.activeShader = null;
	this.activeBuffer = null;
	this.activeFramebuffer = null;

	this.pMatrix = mat4.create();
	this.mvMatrix = mat4.create();
	this.nMatrix = mat4.create();

	this.width = function () {
		if (this.activeFramebuffer) {
			return this.activeFramebuffer.width;
		} else {
			return this.canvas.width;
		}
	}

	this.height = function () {
		if (this.activeFramebuffer) {
			return this.activeFramebuffer.height;
		} else {
			return this.canvas.height;
		}
	}

	this.fail = function () {
		alert ("No Web-GL.")
	}

	this.begin = function (canvasElement) {
		window.addEventListener ("mousewheel", preventEvent);

		this.canvas = document.getElementById(canvasElement);

		if (!this.canvas) {
			alert("Invalid canvas element!");
			return;
		}

		try {
			this.gl = this.canvas.getContext("experimental-webgl");
			if (!this.gl) {
				this.gl = this.canvas.getContext("webgl");
			}
		} catch (e) {
		}

		if (this.gl) {
			this.makeShader ("default", "v-default", "f-default",
			{vertexPosition: "vertexPosition"},
			{pMatrix: "pMatrix",
			 mvMatrix: "mvMatrix",
			 nMatrix: "nMatrix",
			 color: "color"});
			window.onresize();
		} else {
			this.fail();
		}

		this.gl.enable(this.gl.DEPTH_TEST);
	}

	this.useFrameBuffer = function (name) {
		this.gl.bindFramebuffer (this.gl.FRAMEBUFFER, this.framebuffers[name]);
		this.activeFramebuffer = this.framebuffers[name];
	}

	this.createFrameBuffer = function (name, width, height) {
		// Create & bind framebuffer
		var framebuffer = this.gl.createFramebuffer();
		this.gl.bindFramebuffer (this.gl.FRAMEBUFFER, framebuffer);

		// Create & bind texture
		var texture = this.gl.createTexture();
		this.gl.bindTexture (this.gl.TEXTURE_2D, texture);

		// Set texture parameters
		this.gl.texParameteri(this.gl.TEXTURE_2D, this.gl.TEXTURE_MAG_FILTER, this.gl.LINEAR);
		this.gl.texParameteri(this.gl.TEXTURE_2D, this.gl.TEXTURE_MIN_FILTER, this.gl.LINEAR);

		// Set texture size
		this.gl.texImage2D(this.gl.TEXTURE_2D, 0, this.gl.RGBA, width, height, 0, this.gl.RGBA, this.gl.UNSIGNED_BYTE, null);

		// This can fail if the texture is not a power of two
		this.gl.generateMipmap(this.gl.TEXTURE_2D);

		// Create & bind depth buffer
		var renderbuffer = this.gl.createRenderbuffer();
		this.gl.bindRenderbuffer(this.gl.RENDERBUFFER, renderbuffer);

		// Set depth buffer parameters (size mainly)
		this.gl.renderbufferStorage(this.gl.RENDERBUFFER, this.gl.DEPTH_COMPONENT16, width, height);

		// Attach texture & depth buffer to the framebuffer
		this.gl.framebufferTexture2D(this.gl.FRAMEBUFFER, this.gl.COLOR_ATTACHMENT0, this.gl.TEXTURE_2D, texture, 0);
		this.gl.framebufferRenderbuffer(this.gl.FRAMEBUFFER, this.gl.DEPTH_ATTACHMENT, this.gl.RENDERBUFFER, renderbuffer);

		// Unbind everything
		this.gl.bindTexture(this.gl.TEXTURE_2D, null);
		this.gl.bindRenderbuffer(this.gl.RENDERBUFFER, null);
		this.gl.bindFramebuffer(this.gl.FRAMEBUFFER, null);

		framebuffer.texture = texture;
		framebuffer.width = width;
		framebuffer.height = height;

		// Add the framebuffer to the list
		this.framebuffers[name] = framebuffer;
	}

	this.makeShader = function (name, vertexName, fragmentName, attrs, uniforms) {
			var shader = this.linkShader (
				this.getShaderFromElement(vertexName),
				this.getShaderFromElement(fragmentName));
			this.registerShader (name, shader,
								attrs,
								uniforms);

	}

	this.getShaderFromElement = function (elementName) {
		var element = document.getElementById(elementName);
		if (!element)
			return null;

		var source = "";

		var k = element.firstChild;
		while (k) {
			if (k.nodeType == 3)
				source += k.textContent;
			k = k.nextSibling;
		}

		var shader = element.type == "shader/vertex"?
						this.gl.createShader(this.gl.VERTEX_SHADER):
					 (element.type == "shader/fragment"?
						this.gl.createShader(this.gl.FRAGMENT_SHADER):
						null);

		if (!shader)
			return null;

		this.gl.shaderSource (shader, source);
		this.gl.compileShader (shader);

		if (!this.gl.getShaderParameter (shader, this.gl.COMPILE_STATUS)) {
			alert(elementName+'\n'+this.gl.getShaderInfoLog(shader));
			return null;
		}
		return shader;
	}

	this.linkShader = function (vertex, fragment) {
		var linked = this.gl.createProgram();
		this.gl.attachShader (linked, vertex);
		this.gl.attachShader (linked, fragment);
		this.gl.linkProgram(linked);

		if (!this.gl.getProgramParameter (linked, this.gl.LINK_STATUS)) {
			return null;
		}

		return linked;
	}

	this.registerShader = function (name, shader, vars, uniforms) {
		this.shaders[name] = shader;

		shader.uAttributes = {};
		for (var v in vars)  {
			shader.uAttributes[v] = this.gl.getAttribLocation(shader, vars[v]);
		}

		shader.uUniforms = {};
		for (var u in uniforms) {
			shader.uUniforms[u] = this.gl.getUniformLocation(shader, uniforms[u]);
		}
	}

	this.useShader = function (name) {
		this.gl.useProgram (this.shaders[name]);

		if (this.activeShader) {
			for (var a in this.activeShader.uAttributes) {
				this.gl.disableVertexAttribArray(this.activeShader.uAttributes[a]);
			}
		}

		this.activeShader = this.shaders[name];

		for (var a in this.activeShader.uAttributes) {
			this.gl.enableVertexAttribArray(this.activeShader.uAttributes[a]);
		}
	}

	this.createBuffer = function (name, data, size, type, modify) {
		var atype = type?this.gl.ELEMENT_ARRAY_BUFFER:this.gl.ARRAY_BUFFER;

		var buffer = this.gl.createBuffer();
		buffer.size = size;
		buffer.len = data.length/size;
		buffer.atype = atype;
		buffer.modify = modify;

		this.gl.bindBuffer(atype, buffer);

		this.gl.bufferData(atype, data, modify?this.gl.DYNAMIC_DRAW:this.gl.STATIC_DRAW);

		this.buffers[name] = buffer;
	}

	this.updateBuffer = function (name, data) {
		var buffer = this.buffers[name];
		var atype = buffer.atype;
		var modify = buffer.modify;
		this.gl.bindBuffer(atype, buffer);
		this.gl.bufferData(atype, data, modify?this.gl.DYNAMIC_DRAW:this.gl.STATIC_DRAW);
	}

	this.useBuffer = function (name, type) {
		var atype = type==null?this.gl.ELEMENT_ARRAY_BUFFER:this.gl.ARRAY_BUFFER;

		var buffer = this.buffers[name];

		this.gl.bindBuffer(atype, buffer);

		if (type) {
			this.gl.vertexAttribPointer(this.activeShader.uAttributes[type], buffer.size, this.gl.FLOAT, false, 0, 0);
		}

		this.activeBuffer = buffer;
	}

	this.useFrameBufferAsTexture = function (name, uniformname) {
		this.gl.activeTexture(this.gl.TEXTURE0);
		this.gl.bindTexture(this.gl.TEXTURE_2D, this.framebuffers[name].texture);
		this.gl.uniform1i(this.activeShader.uUniforms[uniformname], 0);
	}

	this.viewport = function (w, h, x, y) {
		if (w !== undefined && h !== undefined && x !== undefined && y !== undefined) {
			this.gl.viewport(x, y, w, h);
		}
		else if (w !== undefined && h !== undefined)
			this.gl.viewport(0, 0, w, h);
		else {
			this.gl.viewport(0, 0, render.width(), render.height());
		}
	}

	this.enableBlending = function () {
		this.gl.blendFunc(this.gl.SRC_ALPHA, this.gl.ONE_MINUS_SRC_ALPHA);
		this.gl.enable(this.gl.BLEND);
	}

	this.clear = function () {
		this.gl.clear(this.gl.COLOR_BUFFER_BIT | this.gl.DEPTH_BUFFER_BIT);
	}

	this.clearDepth = function () {
		this.gl.clear(this.gl.DEPTH_BUFFER_BIT);
	}

	this.clearColor = function (color) {
		this.gl.clearColor (color[0],color[1],color[2],color[3]);
	}

	this.draw = function (wireframe) {
		mat4.invert(this.nMatrix, this.mvMatrix);
		mat4.transpose(this.nMatrix, this.nMatrix);

		this.gl.uniformMatrix4fv(this.activeShader.uUniforms.pMatrix, false, new Float32Array (this.pMatrix));
		this.gl.uniformMatrix4fv(this.activeShader.uUniforms.mvMatrix, false, new Float32Array (this.mvMatrix));
		this.gl.uniformMatrix4fv(this.activeShader.uUniforms.nMatrix, false, new Float32Array (this.nMatrix));
		this.gl.drawElements(wireframe?this.gl.LINE_STRIP:this.gl.TRIANGLES, this.activeBuffer.len, this.gl.UNSIGNED_SHORT, 0);
	}

	this.good = function () {
		return true && this.gl;
	}

	this.loadModel = function (model) {
		if (models[model]) {
			models[model]();
			console.info("loaded model '"+model+"'");
		} else {
			console.error("unable to load model '"+model+"'");
		}
	}

	window.onresize = function () {

	}

	/*window.onresize = function () {
		render.canvas.width = window.innerWidth;
		render.canvas.height = window.innerHeight;
	}*/
}

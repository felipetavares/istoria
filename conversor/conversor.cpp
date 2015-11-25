#include <iostream>
#include <fstream>
#include <vector>
#include <string>
using namespace std;

class Vertex {
public:
	unsigned int id;
	float x, y, z;

	Vertex (unsigned int id) {
		x = y = z = 0;
		this->id = id;
	}

	Vertex (unsigned int id, float x, float y, float z) {
		this->x = x;
		this->y = y;
		this->z = z;
		this->id = id;
	}
};

class Face {
public:
	Vertex *v1, *v2, *v3;

	Face () {
		v1 = v2 = v3 = NULL;
	}

	Face (Vertex *v1, Vertex *v2, Vertex *v3) {
		this->v1 = v1;
		this->v2 = v2;
		this->v3 = v3;
	}
};

class Model {
public:
	vector <Vertex> vertices;
	vector <Face> faces;

	Model () {
	}

	void addv (float x, float y, float z) {
		vertices.push_back(Vertex(vertices.size(), x, y, z));
	}

	void addf (unsigned int v1, unsigned int v2, unsigned int v3) {
		Vertex *rv1 = findv(v1);
		Vertex *rv2 = findv(v2);
		Vertex *rv3 = findv(v3);

		if (rv1 != NULL &&
			rv2 != NULL &&
			rv3 != NULL) {
			faces.push_back(Face(rv1, rv2, rv3));
		} else {
			cout << "conversor: reference to inexistent vertex." << endl;
		}
	}
private:
	Vertex* findv (unsigned int position) {
		if (position < vertices.size()) {
			return &vertices[position];
		}
		return NULL;
	}
};

int main (int argc, char** argv) {
	string filename;
	string outname;

	if (argc > 2) {
		filename = string(argv[1]);
		outname = string(argv[2]);
	} else {
		return 1;
	}

	fstream *file = new fstream(filename, ios::in);	

	if (file->is_open()) {
		Model model;
		string command;

		while (file->good()) {
			*file >> command;

			if (command == "v") {
				float x, y, z;

				*file >> x;
				*file >> y;
				*file >> z;

				model.addv(x, y, z);
			} else
				if (command == "f") {
					unsigned int a, b, c;

					*file >> a;
					*file >> b;
					*file >> c;

					model.addf(a-1, b-1, c-1);
				}
		}

		file->close();
	
		delete file;

		file = new fstream(outname, ios::out);

		if (file->is_open()) {
			*file << "models[\"" << outname << "\"] = function () {" << endl;

			*file << "render.createBuffer(\"" << outname << ".vert\"," << " new Float32Array([" << endl;

			for (auto v :model.vertices) {
				*file << v.x << ", " << v.y << ", " << v.z << ", " << endl;
			}

			*file << "])," << endl;
			*file << "3, false);" << endl;

			*file << "render.createBuffer(\"" << outname << ".obj\"," << " new Uint16Array([" << endl;

			for (auto f :model.faces) {
				*file << f.v1->id << ", " << f.v2->id << ", " << f.v3->id << ", " << endl;
			}

			*file << "])," << endl;
			*file << "1, true);" << endl;
			*file << "}" << endl;

			file->close();
		}
	}

	delete file;
	return 0;
}

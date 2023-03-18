//***************************************\\
//              FUNCTIONS
//***************************************\\
function sleep(ms) {
  return new Promise(resolve => setTimeout(resolve, ms));
}

function vox3dModel(path) {
  const loader = new THREE.GLTFLoader();
  
  loader.load(
  path, 

  //Result
  function ( gltf ) {
    getVoxelData(gltf.scene);
    scene.add( gltf.scene );
  }, 

  //Progress
  undefined,

  //Error
  function ( error ) {console.error( error )}
  );
}


function getVoxelData(object) {
  // Setup Voxelizer.
  let options = {
    fill:   false,
    color: true
  };
  const sampler = new Sampler('raycast', options);

  // Voxelize torus.
  const resolution = 250;
  let data = sampler.sample(object, resolution);

  dataToJSON(data);
}


function createTorus() {
  let geometry = new THREE.TorusGeometry( 2, 0.5, 16, 100 );
  let bufferGeometry = new THREE.BufferGeometry().fromGeometry(geometry);
  let material = new THREE.MeshBasicMaterial( { color: 'blue' } );
  let torus = new THREE.Mesh( bufferGeometry, material );

  scene.add( torus );

  renderer.render( scene, camera );

  return torus;
}

function dataToJSON(volume) {
  console.time('Data parsing to JSON');

  //Parse data to voxels
  let voxels = [];
  let nx = volume.voxels.shape[0];
  let ny = volume.voxels.shape[1];
  let nz = volume.voxels.shape[2];

  //Loop over all cells
  for (var i = 0; i < nx; ++i) {
    for (var j = 0; j < ny; ++j) {
      for (var k = 0; k < nz; ++k) {

        if (volume.voxels.get(i, j, k) === 1) {
          let voxel = {};
          voxel.position = { x: i, y: j, z: k };

          // Handle color
          if (volume.colors !== null) {
            let r = volume.colors.get(i, j, k, 0);
            let g = volume.colors.get(i, j, k, 1);
            let b = volume.colors.get(i, j, k, 2);
            voxel.color = { r, g, b };
          }
          voxels.push({ voxel });
        }

      }
    }
  }

  let width = volume.voxels.shape[0];
  let height = volume.voxels.shape[1];
  let depth = volume.voxels.shape[2];

  let dimensions = { dimensions: { width, height, depth } };
  var obj = {
    dimensions: dimensions, 
    voxels: voxels
  };

  console.log(JSON.parse(JSON.stringify(obj)));

  console.timeEnd('Data parsing to JSON');


  JSONtoMinecraft(obj);
}

function JSONtoMinecraft(data) {
  console.log(data);

  //Init color worker
  var worker = new Worker("/public/applications/voxelizer/voxelize-worker.js?v=" + Date.now());

  //Match voxel colors
  var voxels = data.voxels;
  worker.postMessage({
    voxels: JSON.stringify(voxels), 
    colors: colors,
    blocks: blocks
  });

  worker.onmessage = function(e) {
    //Get all voxels with the best matching color
    data.voxels = e.data;

    // console.log(data);
    
    //Request to server to convert to .schematic
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "/public/applications/voxelizer/voxelizer.php");
    xhr.setRequestHeader("Content-Type", "application/json");
    xhr.responseType = "blob";
    xhr.send(JSON.stringify({
        data: data
    }));
    xhr.onreadystatechange = function () {
      if (xhr.readyState == 4 && xhr.status == 200) {
        var response = xhr.response;
        console.log(response);

        try {
          //Download data
          const link = document.createElement("a");
          link.href = URL.createObjectURL(response);
          link.download = "test2.schem";
          link.click();
          link.remove(); 
        } catch (e) {
          console.error("An error occured when downloading the file from the server. Error: " + e);
          return;
        }
      }
    }
  }
}


//****************************************\\
//                  MAIN
//****************************************\\
var colors, blocks;

//Get color data
var xhr = new XMLHttpRequest();
xhr.open("POST", "/public/applications/voxelizer/voxelizer.php");
xhr.setRequestHeader("Content-Type", "application/json");
xhr.send(JSON.stringify({
    "getdata": true
}));
xhr.onreadystatechange = function () {
  if (xhr.readyState == 4 && xhr.status == 200) {
      var response = xhr.response;
      var data = JSON.parse(response);
      colors = data['colors']['Block Textures'];
      blocks = data['blocks'];
  }
}

sleep(10000);

//Define stuff
const { Sampler, XMLExporter } = window.Voxelizer;
var sceneWidth = 1800;
var sceneHeight = 1200;

//Init scene
const scene = new THREE.Scene();
const renderer = new THREE.WebGLRenderer(
  {alpha: true}
);
renderer.setSize( sceneWidth, sceneHeight );
document.querySelector('.tool').appendChild( renderer.domElement );

const camera = new THREE.PerspectiveCamera( 75, sceneWidth / sceneHeight, 0.1, 1000 );
const controls = new THREE.OrbitControls( camera, renderer.domElement );

camera.position.z = 5;

let light = new THREE.HemisphereLight(0xffffbb, 0x080820, 4);
scene.add(light);

//Add custom 3d model and voxelize is
// var path = '/public/applications/voxelizer/den_hoorn_3/untitled.glb';
// var path = '/public/applications/voxelizer/cube/cube.glb';
// var path = '/public/applications/voxelizer/guitar/scene.gltf';
var path = '/public/applications/voxelizer/kasteel/scene.gltf';
// vox3dModel('/public/applications/voxelizer/kasteel/scene.gltf');



//Create 3d object (torus)
// let torus = createTorus();

function animate() {
	requestAnimationFrame( animate );

	controls.update();

	renderer.render( scene, camera );
}
animate();

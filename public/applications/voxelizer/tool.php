<!--Import libs-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r114/three.min.js"></script>
<script src="https://unpkg.com/three@0.114.0/examples/js/loaders/GLTFLoader.js"></script>
<script src="https://unpkg.com/three@0.114.0/examples/js/controls/OrbitControls.js"></script>
<!-- <script src="/library/three.js/three-voxel-loader.js"></script> -->
<script src="https://unpkg.com/voxelizer/lib/voxelizer.js"></script>

<div class="import-3d">
    <label for="upload-3d">Upload your file</label>
    <input type="file" id="upload-3d" oninput="vox3dModel(path)" accept=".glb, .gltf">
    <p id="progress"></p>
</div>
<?php
require 'scripts/encrypt_decrypt.php';
session_start();
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Structural Modeling</title>

        <!-- JointJs Dependencies -->
        <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/jointjs/3.0.4/joint.css" />

        <!-- Bootstrap Dependencies -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
		<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css">

        <!-- Local Dependencies -->
        <link rel="stylesheet" href="css/stylesheet.css">
    </head>
    <body>

        <div style="margin: 10px;" class="row"> <!-- This contains the entire page and adds a margin around everything -->
            <div id="leftCol" class="col-lg-8">
                <!-- Navbar element holding dropdown menus -->
                <nav class="navbar navbar-default">
                    <div class="container-fluid">
                        <div class="navbar-header">
                            <a class="navbar-brand" href="./index.php">IML</a>
                        </div>

                        <!-- Dropdown Menus -->
                        <ul class="nav navbar-nav">
                            <li class="dropdown">
                                <a class="dropdown-toggle" data-toggle="dropdown" href="#">File
                                <span class="caret"></span></a>
                                <ul class="dropdown-menu">
                                    <!-- Add menu options here -->
                                    <li><a id="new" href="#" onclick="newModel()">New Model</a></li>
									<!--<li><a id="save" href="#" onclick="">Save</a></li>-->
                                    <li><a id="import" href="#" onclick="downloadModelFile('importFile')">Import Existing Model</a></li>
									<li><a id="export" href="#" onclick="exportCurrentModel()">Export Current Model (.iml)</a></li>
									<li class="dropdown-submenu">
										<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"> <span class="nav-label">Export Model as Image...</span><span class="caret"></span></a>
										<ul id="imageExport" class="dropdown-menu">
											<!-- Add menu options here -->
											<li><a id="imagePNG" href="#" onclick="exportAsPNG()">Export as Image (.png)</a></li>
											<li><a id="imageSVG" href="#" onclick="exportAsSVG()">Export as Vector Graphic (.svg)</a></li>
										</ul>
									</li>
									
                                    
                                </ul>
                            </li>
                            <li class="dropdown">
                                <a class="dropdown-toggle" data-toggle="dropdown" href="#"role="button" aria-haspopup="true" aria-expanded="true"> <span class="nav-label">Model Design</span>
                                <span class="caret"></span></a>
                                <ul class="dropdown-menu">
									<li id="create" class="disabled"><a href="#" onclick="createInstance()">Create Instance From Current Model</a></li>
                                    <li id="check" class="disabled"><a href="#" onclick="reportConformance()">Check Meta-Model Conformance</a></li>
									<li class="dropdown-submenu">
										<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"> <span class="nav-label">Routing Options</span><span class="caret"></span></a>
										<ul id="routingOptions" class="dropdown-menu">
											<!-- Add menu options here -->
											<li><a href="#" id="simpleRoute" class="selected" onclick="simpleRoute()"><i class="fa fa-check"></i> <span class="search-option">Simple Routing</span></a></li>
											<li><a href="#" id="orthogonalRoute" onclick="orthogonalRoute()"><i class="fa fa-check"></i> <span class="search-option">Orthogonal Routing</span></a></li>
											<li><a href="#" id="manhattanRoute" onclick="manhattanRoute()"><i class="fa fa-check"></i> <span class="search-option">Manhattan Routing</span></a></li>
											<li><a href="#" id="metroRoute" onclick="metroRoute()"><i class="fa fa-check"></i> <span class="search-option">Metro Routing</span></a></li>
										</ul>
									</li>
                                </ul>
                                <input id="importFile" style="display: none;" type="file" accept=".iml" onclick="this.value = null" onchange="readFile(this)" />
								<input id="readMeta" style="display: none;" type="file" accept=".iml" onclick="this.value = null" onchange="newConform(this)" />
                            </li>
                            <li class="dropdown">
                                <a class="dropdown-toggle" data-toggle="dropdown" href="#">Model Transformations
                                <span class="caret"></span></a>
                                <ul class="dropdown-menu">
                                    <!-- Add menu options here -->
                                    <li><a id="generate" href="#" onclick="generateCode()">Generate Java Code</a></li>
                                </ul>
                            </li>
                        </ul>

                    </div>
                </nav>

                <!-- Remaining of left column containing the JointJS Paper. -->
                <div id="paperHolder" class="panel panel-default grabbable" ondrop="drop(event)" ondragover="allowDrop(event)"></div>
                <div id="zoomScale"></div>
            </div>

            <div class="col-lg-4">



                <!-- Palette element containing modeling tools for the given metamodel -->
                <div class="row">
                    <div style="height:300px; margin-bottom: 10px;" class="panel panel-default">
                        <div class="panel-heading text-center">Palette</div>
                        <div id="paletteIcons" class="palette"></div>
                    </div>
                </div>
                
				                <!-- Model Conformance element -->
                <div class="row" id="conformanceBlock" style="height:150px; margin-bottom: 10px; display:none"> 
                    <div class="panel panel-default" style="height:150px;">
                        <div class="panel-heading text-center">Meta-Model Conformance</div>
						<div class="model-conformance" id="model-conformance">
							<!--  -->
							  <div class="float-childA">
								<div id="conformIcon" style="display: none;" class="conformIcon">&check;</div>
							  </div>
							  <div class="float-childB">
								<div id="conformanceErrors" class="conformanceErrors"></div>
							  </div>
						</div>
                    </div>
                </div>
				
                <!-- Property/Value table containing the properties of the selected element -->
                <div class="row full-height">
                    <div class="panel panel-default">
                        <div class="panel-heading text-center">Properties Table</div>
                        <table id="propertiesTable" class="table table-bordered" style="text-align: left;"> 
                            <thead>
                                <tr style="height:auto;"> 
                                    <th class="uneditableCell propertyTableCell">Property</th> 
                                    <th class="uneditableCell propertyTableCell">Value</th> 
                                </tr>
                            </thead>
                            <tbody id="properties">

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

<div hidden id="d"></div>

        
        <!-- JointJs Dependencies -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.js"></script> <!-- Bootstrap 3 Depends on this version of JQuery as well. -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.14/lodash.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/backbone.js/1.4.0/backbone.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jointjs/3.0.4/joint.js"></script>

        <!-- Bootstrap 3 JavaScript Dependencies -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>

        <!-- Custom JavaScript -->

            <!-- Classes -->
            <script type="text/javascript" src="classes/ImlAttribute.js"></script>
            <script type="text/javascript" src="classes/ImlClass.js"></script>
            <script type="text/javascript" src="classes/ImlStructuralModel.js"></script>
			<script type="text/javascript" src="classes/ImlAction.js"></script>
            <script type="text/javascript" src="classes/Enumerations/ImlRelationType.js"></script>
            <script type="text/javascript" src="classes/Enumerations/ImlType.js"></script>
            <script type="text/javascript" src="classes/Enumerations/ImlVisibilityType.js"></script>
			<script type="text/javascript" src="classes/Enumerations/ImlActionType.js"></script>
			<script type="text/javascript" src="classes/Enumerations/ImlActionTargetType.js"></script>
            <!-- Relation must be defined first before it can be inherited -->
            <script type="text/javascript" src="classes/Relations/ImlRelation.js"></script>
            <script type="text/javascript" src="classes/Relations/ImlInheritance.js"></script>
            <script type="text/javascript" src="classes/Relations/ImlBoundedRelation.js"></script>
            
            <script type="text/javascript" src="classes/Relations/ImlReference.js"></script>
            <script type="text/javascript" src="classes/Relations/ImlComposition.js"></script>
            
            <!-- Main -->
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.5.0/jszip.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.2/FileSaver.min.js"></script>
			<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
			<script type="text/javascript" src="scripts/validation.js"></script>
            <script type="text/javascript" src="scripts/CustomElements.js"></script>
            <script type="text/javascript" src="scripts/ClassElementHelpers.js"></script>
            <script type="text/javascript" src="scripts/export.js"></script>
            <script type="text/javascript" src="scripts/generateCode.js"></script>
            <script type="text/javascript" src="scripts/import.js"></script>
			<script type="text/javascript" src="scripts/main.js"></script>

        <!--  -->

    </body>
</html>
<?php


if(isset($_GET['project']) && !isset($_SESSION['structModelLoaded']) ){
	error_log('TEST');       
	$projectPath = encrypt_decrypt('decrypt',$_GET['project']);
    $data = file_get_contents($projectPath);
    $pathArray = explode('/',$projectPath);
    $name = $pathArray[count($pathArray) - 1];
    $loadModel = "<script language='javascript' type='text/javascript'> $(document).ready(function(){ implementPaper(); showModelProperties(); importModel(".JSON_ENCODE($data).",".JSON_ENCODE($name).",false,graph); }); </script>"; 
    $_SESSION['structModelLoaded'] = true ;
}
else{
    $loadModel = "<script language='javascript' type='text/javascript'> $(document).ready(function() {
        implementPaper();
        determineEditMode();
        showModelProperties();
        });  </script>";
}

echo $loadModel;

?>



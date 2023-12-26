var sourceMetamodelPaper = new joint.dia.Paper;
var sourceMetamodelGraph = new joint.dia.Graph;
var targetMetamodelPaper = new joint.dia.Paper;
var targetMetamodelGraph = new joint.dia.Graph;

var sourceMetamodel = new ImlStructuralModel();
var targetMetamodel = new ImlStructuralModel();
var instanceModel = new ImlStructuralModel();

var sourceMetamodelRoutingMode = 'simpleRoute';
var targetMetamodelRoutingMode = 'simpleRoute';

var attributes = new Map(); // Allows easy access for editing attributes without going through the IML Class.
var attributeViews = new Map(); // Stores the views of the attributes to access for unhighlight.

var isSourceMetamodelLoaded = false;
var isTargetMetamodelLoaded = false;
var areTransformationsRules = false;
var areInputModels = false;

var rules = new Map();
var rulesId = 0;

$(document).ready(function() {
	implementPapers();
});

function addAttributeNames(attributeNamesButton) {
	// Enable all buttons in row, then disable current button.
	enableNamesButtons();
	attributeNamesButton.disabled = true;

	// Clear the dropdown before adding.
	sourceDropdown = $("#sourceNames");
	sourceDropdown.html("");

	sourceMetamodel.classes.forEach(imlClass => {
		imlClass.attributes.forEach(attribute => {
			selectAttribute = new Option(attribute.name, attribute.id);
			$(selectAttribute).html(imlClass.name + " : " + attribute.name);
			sourceDropdown.append(selectAttribute);
		});
	});
	
	// Clear the dropdown before adding.
	targetDropdown = $("#targetNames");
	targetDropdown.html("");

	// Add class names to select
	targetMetamodel.classes.forEach(imlClass => {
		imlClass.attributes.forEach(attribute => {
			selectAttribute = new Option(attribute.name, attribute.id);
			$(selectAttribute).html(imlClass.name + " : " + attribute.name);
			targetDropdown.append(selectAttribute);
		});
	});
}

function addClassNames(classNamesButton) {
	// Enable all buttons in row, then disable current button.
	enableNamesButtons();
	classNamesButton.disabled = true;

	// Clear the dropdown before adding.
	sourceDropdown = $("#sourceNames");
	sourceDropdown.html("");

	// Add class names to select
	sourceMetamodel.classes.forEach(imlClass => {
		selectClass = new Option(imlClass.name, imlClass.id);
		sourceDropdown.append(selectClass);
	});

	// Clear the dropdown before adding.
	targetDropdown = $("#targetNames");
	targetDropdown.html("");

	// Add class names to select
	targetMetamodel.classes.forEach(imlClass => {
		selectClass = new Option(imlClass.name, imlClass.id);
		targetDropdown.append(selectClass);
	});
}

function addRelationNames(relationNamesButton) {
	// Enable all buttons in row, then disable current button.
	enableNamesButtons();
	relationNamesButton.disabled = true;

	// Clear the dropdown before adding.
	sourceDropdown = $("#sourceNames");
	sourceDropdown.html("");

	sourceMetamodel.relations.forEach(relation => {
		if(relation instanceof ImlBoundedRelation) {
			selectRelation = new Option(relation.name, relation.id);
			sourceDropdown.append(selectRelation);
		}
	});

	targetDropDown = $("#targetNames");
	targetDropDown.html("");

	targetMetamodel.relations.forEach(relation => {
		if(relation instanceof ImlBoundedRelation) {
			selectRelation = new Option(relation.name, relation.id);
			targetDropDown.append(selectRelation);
		}
	});
}

function addPaperFunctions(paper) {
    /**
     *  Zoom paper in/out when the cursor is over a blank area.
     */
    paper.on('blank:mousewheel', function(evt, x, y, delta) {
        zoomPaper(delta, paper, .1, 3.0);
    });

    /**
     *  Zoom paper in/out when the cursor is over a cell.
     */
    paper.on('cell:mousewheel', function(cellView, evt, x, y, delta) {
        zoomPaper(delta, paper, .1, 3.0);
	});
	
	paper.on('blank:pointerdown', function(evt, x, y) { 
        // Records the pointer position for dragging the paper
        pointerStart = { 'x': x, 'y': y }
    });

    var pointerStart;
    /**
     *  Drag paper according to the current position of the cursor
     *  and a recorded start point of the cursor.
     */
    paper.on('blank:pointermove', function(evt, x, y) {

		const htmlPaperHolder = paper.el
		dragPaper(paper, $(htmlPaperHolder), x, y, pointerStart, true);
		
    });

    /**
     *  Set cursor back to default after drag has finished.
     */
    paper.on('blank:pointerup', function(evt, x, y) { 
        
		const htmlPaperHolder = paper.el
        $(htmlPaperHolder).css('cursor', 'default');

	});
}

function clearInstanceModel() {
	$('#instanceModelLoadButton').attr('disabled', true);
	$('#instanceModelEditButton').attr('disabled', true);
	$("#instanceModelArrowBeforeButton").attr('src', 'Resources/modelTransformation/RightArrowFail.svg');
	updateFailure($("#instanceModelPanel"));
	instanceModel = new ImlStructuralModel();
}

function clearRules() {
	areTransformationsRules = false;
	rules.clear(); // Clear rules map.
	$("#rulesList").empty(); // Clear rules in UI.
	// rulesId = 0; Reset rules id counter. May not be needed.
}

function createMappingRule() {
	sourceElementId = $("#sourceNames").val();
	targetElementId = $("#targetNames").val();
	mappingRule = new MappingRule(sourceMetamodel.getElement(sourceElementId), targetMetamodel.getElement(targetElementId));

	if(isDuplicateRule(rules, mappingRule)) {
		swal("IML: Duplicate Rule Error", mappingRule.toString() + " has already been created.", "error").then(function(){
			openMapRules();
		});
	} else {
		// If empty, first rule so update success in UI.
		if(rules.size == 0) {
			areTransformationsRules = true;
			updateSuccess($("#transformationPanel"));
			$("#downArrow").attr('src', 'Resources/modelTransformation/DownArrowSuccess.svg');

			// If there are instance models, then allow transformations. 
			if(areInputModels) {
				$("#instanceModelArrowAfterButton").attr('src', 'Resources/modelTransformation/RightArrowSuccess.svg');
				updateSuccessButton($("#transformButton"));
			}
		}

		position = rulesId;

		// Add rule to rules map.
		rules.set(position, mappingRule);

		// Add rule to UI.
		ruleElement = 	'<li id=' + position + ' class="list-group-item" style="margin: 3px; padding:3px; font-size: 12px;">' + 
							mappingRule.toString() + 
							'<span class="badge btn btn-danger" style="padding: 2px 4px; border-color: grey;" onclick="removeRule(this)">X</span>' + 
						'</li>'

		$("#rulesList").append(ruleElement);
		rulesId++;
	}
}

function isDuplicateRule(rules, checkRule) {
	isFound = false;
	rules.forEach(rule => {
		if(rule.equals(checkRule)) {
			isFound = true;
		}
	});

	return isFound;
}

function removeRule(deleteRuleElement) {
	// Get rule list element and key in map.
	ruleElement = $(deleteRuleElement).parent();
	position = Number(ruleElement.attr('id'));

	// Remove from backend and UI
	rules.delete(position);
	ruleElement.remove();

	// If rules is empty, update failure arrow.
	if(rules.size == 0) {
		areTransformationsRules = false;
		$("#downArrow").attr('src', 'Resources/modelTransformation/DownArrowFail.svg');
		updateFailure($("#transformationPanel"));
		// If there are instance models, then disallow transformations.
		if(areInputModels) {
			$("#instanceModelArrowAfterButton").attr('src', 'Resources/modelTransformation/RightArrowFail.svg');
			updateFailureButton($("#transformButton"));
		}
	}
}

/**
 * Called by "Import Existing Model..." to accept .iml file. 
 * This method handles the file uploading events.
 * 
 * @param {Input<file>} elemId 
 */
function downloadModelFile(elemId) {
    // Code provided by from StackOverflow answer: https://stackoverflow.com/a/6463467
    var elem = document.getElementById(elemId);
    if(elem && document.createEvent) {
        var evt = document.createEvent("MouseEvents");
        evt.initEvent("click", true, false);
        elem.dispatchEvent(evt);
    }
}

function enableNamesButtons() {
	buttonsRow = $("#namesButtons");
	buttonsRow.children().removeAttr("disabled");
}

/**
  * Called by the input element onchange event, signifying an instance model file has been upload.
  * This method reads the text of the upload file and attempts to convert it to an IML instance model in runtime.
  * 
  * @param {Input<file>} importFileElement 
  */
function loadInstanceModels(importFileElement) {
    // Code for reading file text provided by Javascript.info: https://javascript.info/file		
	var reader = new FileReader();
	var inputFile = importFileElement.files[0]; // Always be an array of 1, input element does not have multiple tag.
	reader.readAsText(inputFile);

	reader.onload = function() {
        var fileType = inputFile.name.split('.')[1];
        if(fileType.localeCompare("iml") == 0) {
			instanceModel = importInstanceModel(reader.result, inputFile.name);
		} else {
			// TODO: Convert this to sweet alert.
            window.alert('File uploads must be of type ".iml"');
		}

	};
}

/**
  * Called by the input element onchange event, signifying a metamodel file has been upload.
  * This method reads the text of the upload file and attempts to convert it to an IML metamodel in runtime.
  * 
  * @param {Input<file>} importFileElement 
  */
function loadMetamodel(importFileElement) {
    // Code for reading file text provided by Javascript.info: https://javascript.info/file			   
	var reader = new FileReader();
    var inputFile = importFileElement.files[0]; // Always be an array of 1, input element does not have multiple tag.
	reader.readAsText(inputFile);

    reader.onload = function() {
        var fileType = inputFile.name.split('.')[1];
        if(fileType.localeCompare("iml") == 0) {
			if(importFileElement.id == "importSourceMetamodelFile") { // Importing source metamodel
				sourceMetamodel = importModel(reader.result, inputFile.name, false, sourceMetamodelGraph, sourceMetamodelPaper);
				clearInstanceModel(); // Whether or not import fails, the instance model should be cleared for the new metamodel.
				if(sourceMetamodel) { // Check if import failed.
					updateSuccess($("#sourceMetamodelPanel"));
					updateSuccess($("#sourceMetamodelDisplayPanel"));
					$("#sourceMetamodelToRules").attr('src', 'Resources/modelTransformation/SourceMMtoRulesSuccess.svg');
					$('#instanceModelLoadButton').removeAttr('disabled');
					$('#instanceModelEditButton').removeAttr('disabled');
					isSourceMetamodelLoaded = true;
				} else { // Failed
					updateFailure($("#sourceMetamodelPanel"));
					updateFailure($("#sourceMetamodelDisplayPanel"));
					$("#sourceMetamodelToRules").attr('src', 'Resources/modelTransformation/SourceMMtoRulesFail.svg');
					// Disables rule buttons
					disableRulesButtons();
					isSourceMetamodelLoaded = false;
				}
            } else if(importFileElement.id == "importTargetMetamodelFile") { // Importing target metamodel
				targetMetamodel = importModel(reader.result, inputFile.name, false, targetMetamodelGraph, targetMetamodelPaper);
				if(targetMetamodel) { // Check if import failed.
					updateSuccess($("#targetMetamodelPanel"));
					updateSuccess($("#targetMetamodelDisplayPanel"));
					$("#targetMetamodelToRules").attr('src', 'Resources/modelTransformation/TargetMMtoRulesSuccess.svg');
					isTargetMetamodelLoaded = true;
				} else { // Failed
					updateFailure($("#targetMetamodelPanel"));
					updateFailure($("#targetMetamodelDisplayPanel"));
					$("#targetMetamodelToRules").attr('src', 'Resources/modelTransformation/TargetMMtoRulesFail.svg');
					// Disables rule buttons
					disableRulesButtons();
					isTargetMetamodelLoaded = false;
				}
			}
			
			if(isSourceMetamodelLoaded && isTargetMetamodelLoaded) {
				// Enables rule buttons
				enableRulesButtons();
				$(".ruleButton").addClass('btn btn-default btn-model-box');
			}

			// If there are rules, then everything after can be set to failure (except instance model).
			if(areTransformationsRules) {
				clearRules();
				$("#downArrow").attr('src', 'Resources/modelTransformation/DownArrowFail.svg');
				updateFailure($("#transformationPanel"));
				$("#instanceModelArrowAfterButton").attr('src', 'Resources/modelTransformation/RightArrowFail.svg');
				updateFailureButton($("#transformButton"));
			}
        } else {
			// TODO: Convert this to sweet alert.
            window.alert('File uploads must be of type ".iml"');	  
        }	
    }

    reader.onerror = function() {
        console.log(reader.error);
    };
}

function implementPapers() {
    sourceMetamodelPaper = new joint.dia.Paper({
        el          :       $("#sourceMetamodelDisplay"),   // Adds the paper to an element in the DOM.
        model       :       sourceMetamodelGraph,           // Adds the graph to the paper.
        height      :       null,                           // Allows CSS to control height of paper
        width       :       null,                           // Allows CSS to control width of paper
        gridSize    :       1,
        interactive :       false,                          // No interactions for displays
		restrictTranslate:  true,
	});
	
	addPaperFunctions(sourceMetamodelPaper);
	
    targetMetamodelPaper = new joint.dia.Paper({
        el          :       $("#targetMetamodelDisplay"),   // Adds the paper to an element in the DOM.
        model       :       targetMetamodelGraph,           // Adds the graph to the paper.
        height      :       null,                           // Allows CSS to control height of paper
        width       :       null,                           // Allows CSS to control width of paper
        gridSize    :       1,
        interactive :       false,                          // No interactions for displays
		restrictTranslate:  true,
	});

	addPaperFunctions(targetMetamodelPaper);
	
}

function importInstanceModel(xmlString, fileName) {

	inputModel = new ImlStructuralModel();

	try {
        xmlDoc = $.parseXML(xmlString);
		$xml = $(xmlDoc);
		
		$structuralModelXml = $xml.find("StructuralModel");
		instanceModelConformsTo = $structuralModelXml.attr("conformsTo");

		// Ensure uploaded model is an instance model.
		if(instanceModelConformsTo == "IML Definition"){
			throw new Error("The selected model file (" + fileName + ") is not an instance model");
		}

		// Ensure uploaded instance model conforms to the uploaded metamodel.
		sourceMetamodelConformsTo = sourceMetamodel.fileName;
		if(instanceModelConformsTo != sourceMetamodelConformsTo) {
			throw new Error("The instance model " + fileName + " must conform to the current source metamodel " + sourceMetamodelConformsTo);
		}

		// NOTE: We do not need to check for the existence of a source metamodel because this method will be blocked
		// due to greyed out buttons in the instance model panel until a source metamodel has been uploaded.

		inputModel.setFileName(fileName);
		inputModel.setConformsTo($structuralModelXml.attr("conformsTo"));
		inputModel.setName($structuralModelXml.attr("name"));

		// Adds classes to the given input model (This adds attributes too).
		createRuntimeClasses($structuralModelXml.find("Classes"), inputModel, true);
		// Add relations to given input model. The corresponding classes are accessed through inputModel.
		createRuntimeRelations($structuralModelXml.find("Relations"), inputModel, true);

		instanceModel = inputModel;
		
		// Update success in UI.
		updateSuccess($("#instanceModelPanel"));
		$("#instanceModelArrowBeforeButton").attr('src', 'Resources/modelTransformation/RightArrowSuccess.svg');
		areInputModels = true;

		// If there are transformation rules, then allow for transformations.
		if(areTransformationsRules) {
			$("#instanceModelArrowAfterButton").attr('src', 'Resources/modelTransformation/RightArrowSuccess.svg');
			updateSuccessButton($("#transformButton"));
		}

	} catch (error) {
        console.log(error);
        var msg = error.toString().replace("Error: ", "") + " - Instance Model Import has been aborted";
		swal("IML: Instance Model Import Error", msg, "error");
		updateFailure($("#instanceModelPanel"));
		$("#instanceModelArrowBeforeButton").attr('src', 'Resources/modelTransformation/RightArrowFail.svg');
    }
}

/**
 * Called after the text of the input file has been read.
 * This method establishes the backend for the import model.
 * 
 * @param {String} xmlString 
 */
function importModel(xmlString, fileName, isMeta, graph, paper) {

	inputModel = new ImlStructuralModel();
    graph.removeCells(graph.getElements().filter(function(e1){return e1.attributes.type!="iml.Point";}));
	
    try {
        xmlDoc = $.parseXML(xmlString);
        $xml = $(xmlDoc);

		$structuralModelXml = $xml.find("StructuralModel");
        if($structuralModelXml) { // Check for a structural model in the iml file
            // Read in classes
			if($structuralModelXml.attr("conformsTo") != "IML Definition")
				throw new Error("The selected model file (" + fileName + ") is not a metamodel");

			inputModel.setFileName(fileName);
			inputModel.setConformsTo($structuralModelXml.attr("conformsTo"));
			inputModel.setName($structuralModelXml.attr("name"));
			
            $classes = $structuralModelXml.find("Classes"); 
            $classes.find("Class").each(function(index, xmlClass) {

				//check to see if the class has attributes for name, isAbstract, x, y 
				if(!xmlClass.hasAttribute("name")){
					throw new Error('The ' + ordinal_suffix_of(index+1) + ' IML Class is missing the "name" attribute.');
				}
				if(!xmlClass.hasAttribute("isAbstract")){
					throw new Error('IML Class ' + xmlClass.attributes["name"].value + ' is missing the "isAbstract" attribute.');
				}
				if(!xmlClass.hasAttribute("x")){
					throw new Error('IML Class ' + xmlClass.attributes["name"].value + ' is missing the "x" attribute.');
				}
				if(!xmlClass.hasAttribute("y")){
					throw new Error('IML Class ' + xmlClass.attributes["name"].value + ' is missing the "y" attribute.');
				}
				if(!xmlClass.hasAttribute("id")){
					throw new Error('IML Class ' + xmlClass.attributes["name"].value + ' is missing the "id" attribute.');
				}
			
				//check to see if the class name attribute is blank
				var className = xmlClass.attributes["name"].value;
				if(!className) {
					throw new Error('The ' + ordinal_suffix_of(index+1) + ' IML Class is missing the "name" attribute.');
				}
				//check to make sure the class name is unique
				if(duplicateName(className,inputModel.classes)){
					throw new Error('There are duplicate definitions of Class ' + className + '; IML requires unique class names');
				}
				//check to make sure the isAbstract value is not blank, and a valid boolean value
				var isAbstract = xmlClass.attributes["isAbstract"].value;
				if(!isAbstract) {
					throw new Error('Class "' + className + '" is missing the "isAbstract" attribute.');
				}
				if(!validBool(isAbstract)){
					throw new Error('Class "' + className + '" has an invalid Boolean value for the isAbstract attribute.')
				}
				if (isAbstract == "TRUE")
					isAbstract = true;
				else
					isAbstract = false;
				
				var classID = xmlClass.attributes["id"].value;
				
				var imlClassView = new ImlClassElement();
				imlClassView.attr({
					classNameLabel: {
						text: className, 
					},
				});

				// Set the view id to the class id.
				imlClassView.id = classID;
				imlClassView.attributes.id = classID;
				
				
				var imlClass = new ImlClass(className, new Map(), new Map(), isAbstract, classID);
				inputModel.addClass(imlClass);
				
				//check to make sure x and y positions are valid positional data then position the class
				var xPos = Number(xmlClass.attributes["x"].value);
				var yPos = Number(xmlClass.attributes["y"].value);
				if(!validPosition(xPos)){
					throw new Error('Class "' + className + '" has an invalid Integer value for the X position attribute.')
				}
				if(!validPosition(yPos)){
					throw new Error('Class "' + className + '" has an invalid Integer value for the Y position attribute.')
				}
				imlClassView.position(xPos, yPos);
				
				if(imlClass.isAbstract == true){
					imlClassView.attributes.attrs.classNameLabel.text = '<< ' + imlClass.name + ' >>';
					imlClassView.attributes.attrs.classAttributeRect.fill = 'rgba(224,224,224,0.6)';
					imlClassView.attributes.attrs.classNameLabel.fontStyle =	'italic';
				}
				else{ 
					imlClassView.attributes.attrs.classNameLabel.text = imlClass.name;
					imlClassView.attributes.attrs.classAttributeRect.fill = 'rgba(204,229,255,1.0)';
					imlClassView.attributes.attrs.classNameLabel.fontStyle =	'normal';
				}
                graph.addCell(imlClassView);

				// Implement Attributes in the class
				createAttributes($(xmlClass).find("Attribute"), imlClass, imlClassView, false, graph);
				centerAndResizeAttributeViewsOnClass(imlClass, inputModel, paper);
            });
			
			createRelations($structuralModelXml, false, graph, false);
			// Ensures content is not cut off. May need to be increased if models appear too large.
			paper.scaleContentToFit({ padding: 2.5 });
			
			return inputModel;
        }
    } catch (error) {
        console.log(error);
		graph.removeCells(graph.getElements().filter(function(e1){return e1.attributes.type!="iml.Point";}));
        var msg = error.toString().replace("Error: ", "") + " - Meta-Model Import has been aborted";
        swal("IML: Meta-Model Import Error", msg, "error");
    }

}

function openConvertRules() {
	modal = $("#convertRulesModal");
	modal.modal({ backdrop: false });
}

/**
 * 
 * @param {ImlStructuralModel} sourceMetamodel 
 */
function openMapRules() {
	modal = $("#mapRulesModal");
	modal.modal({ backdrop: false });

	addClassNames(document.getElementById("classNamesButton"));
}

function updateFailure(panelElement) {
	panelElement.removeClass("panel-success");
	panelElement.addClass("panel-danger");
}

function updateSuccess(panelElement) {
	panelElement.removeClass("panel-danger");
	panelElement.addClass("panel-success");
}

function updateFailureButton(btnElement) {
	btnElement.removeClass("btnSuccess");
	btnElement.addClass("btnFailure");
	btnElement.attr('disabled', true);
}

function updateSuccessButton(btnElement) {
	btnElement.removeClass("btnFailure");
	btnElement.addClass("btnSuccess");
	btnElement.removeAttr('disabled');
}

function enableRulesButtons() {
	$("#mapRuleButton").attr('disabled', false);
	$("#convertRuleButton").attr('disabled', false);
}

function disableRulesButtons() {
	$("#mapRuleButton").attr('disabled', true);
	$("#convertRuleButton").attr('disabled', true);
}

function openForm() {
	$('#sourceClasses').append(`<option value="Student">Student</option>`);
	$('#targetClasses').append(`<option value="Person">Person</option>`);
	document.getElementById("mappingRules").style.display = "block";
}
  
function closeForm() {
	document.getElementById("mappingRules").style.display = "none";
}
function centerAndResizeAttributeViewsOnClass(imlClass,model=imlStructuralModel, paper) {
	imlClass = model.classes.get(imlClass.id);
	const imlClassView = paper.findViewByModel(imlClass);
    const attrPadding = 10;
    const attrHeightOffset = 25;
    const classNamePadding = 15;
	const iconPad = 25;
	// console.log(imlClassView.attr('classNameLabel'));
    var classNameWidth = imlClassView.selectors.classNameLabel.textLength.baseVal.value;
	var maxAttributeNameWidth = getMaxAttributeNameWidth(imlClass, paper) + iconPad;
	
	var resizedAttributeWidth = 0;
    if(classNameWidth > maxAttributeNameWidth) {
        classNameWidth += classNamePadding;
        resizedAttributeWidth = classNameWidth - attrPadding;
        
        imlClassView.model.attributes.attrs.classAttributeRect.width = (classNameWidth);
		imlClassView.resize(imlClassView.model.attributes.attrs.classAttributeRect.height, classNameWidth);
    } else { // maxAttribute is wider
		resizedAttributeWidth = maxAttributeNameWidth;

        imlClassView.model.attributes.attrs.classAttributeRect.width = (maxAttributeNameWidth + attrPadding);
		imlClassView.resize(imlClassView.model.attributes.attrs.classAttributeRect.height, maxAttributeNameWidth + attrPadding);
    }
	
	classNameWidth = imlClassView.selectors.classNameLabel.textLength.baseVal.value + classNamePadding;
	var maxWidth = Math.max(classNameWidth,resizedAttributeWidth);
	imlClassView.model.attributes.attrs.classAttributeRect.width = maxWidth + attrPadding;
    imlClassView.resize(imlClassView.model.attributes.attrs.classAttributeRect.height, maxWidth + attrPadding);

    var attributeCounter = 1;

	
    imlClass.attributes.forEach(attribute => {
        // Get the SVG element from the paper
        const attributeView = attributeViews.get(attribute.id);
        const attributePaperView = paper.findViewByModel(attributeView);
        // Resize each attribute according to class width
        attributePaperView.model.attributes.attrs.attributeRect.width = maxWidth;
		attributeView.resize(maxWidth, attrHeightOffset);
		
        // Center attribute on class
        const centeringOffset = 5;
        attributeView.position( 
                                imlClassView.model.attributes.position.x + centeringOffset, 
                                imlClassView.model.attributes.position.y + (attrHeightOffset * attributeCounter)
                              );
        attributeCounter++;
	});

	
    
	
	// Ensure the embeds array is not undefined. If there are no embeds in the class, the embeds array will not appear in attributes.
	if(imlClassView.model.attributes.embeds) {
		imlClassView.model.attributes.embeds.forEach(obj =>{
			var rel = model.relations.get(obj);
			if (rel)// && (rel.source.localeCompare(rel.destination)==0))
				redrawSelfRelation(imlClass,rel);
		});
	}
	
	//ensure class size matches the attributes rectangle size
	imlClassView.model.attributes.size.height = imlClassView.model.attributes.attrs.classAttributeRect.height;
	imlClassView.model.attributes.size.width = imlClassView.model.attributes.attrs.classAttributeRect.width;
}

function getMaxAttributeNameWidth(imlClass, paper) {
    const attrMargin = 10;
    var maxAttributeWidth = 0;

	if (imlClass.attributes){
		imlClass.attributes.forEach(attribute => {
			// Get the SVG element from the paper
			const attributePaperView = paper.findViewByModel(attributeViews.get(attribute.id));
			// Get the length of the attribute text element
			var attributeNameWidth = attributePaperView.selectors.attributeLabel.firstChild.textLength.baseVal.value + attrMargin;
			// Keep track of the largest length
			if(attributeNameWidth > maxAttributeWidth) { maxAttributeWidth = attributeNameWidth; }
		});
		

		return maxAttributeWidth;
	}
	else
		return 0;
}

function getClassWidthNoAttribute(imlClass) {
    var classElementView = paper.findViewByModel(classes.get(imlClass.id));
    classNameWidth = classElementView.selectors.classNameLabel.textLength.baseVal.value + classNamePadding;

    return classNameWidth;
}
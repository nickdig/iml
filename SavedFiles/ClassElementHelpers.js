function getMaxAttributeNameWidth(imlClass) {
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
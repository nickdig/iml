class MappingRule extends TransformationRule {
    sourceElement; targetElement;

    /**
     * Accepts two elements of an IML metamodel. The source element provides
     * necessary information for searching for elements of that type when applying 
     * a mapping rule. The target element provides necessary information for creating
     * an element in the target instance model once a source element is found.
     * 
     * @param {ImlClass | ImlAttribute | ImlRelation} sourceElement 
     * @param {ImlClass | ImlAttribute | ImlRelation} targetElement 
     */
    constructor(sourceElement, targetElement) {
        super();
        this.sourceElement = sourceElement;
        this.targetElement = targetElement;
    }

    toString() {
        return this.sourceElement.name + " maps to " + this.targetElement.name;
    }

    equals(checkRule) {
        if(this.sourceElement.id == checkRule.sourceElement.id && this.targetElement.id == checkRule.targetElement.id) {
            return true;
        }

        return false;
    }

    ///////////////////////////// Getters /////////////////////////////////

    get sourceElement() {
        return this.sourceElement;
    }

    get targetElement() {
        return this.targetElement;
    }
}
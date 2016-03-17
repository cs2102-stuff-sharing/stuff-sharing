function makeAccordion(elementSelector) {
	$(elementSelector).accordion({
        active: false,
        collapsible: true,
        heightStyle: 'content'
    });
}

$(document).ready(function() {
	makeAccordion('.accordionSection');
});
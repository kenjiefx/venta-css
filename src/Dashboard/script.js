$(document).ready(function(){
    let previousPointer = null;
    let previousGroupItem = null;
    const showSelectorView = function(viewPointer) {
        $('[data-selector-pointer="'+viewPointer+'"]').fadeIn();
        if (null!==previousPointer) {
            $('[data-selector-pointer="'+previousPointer+'"]').hide();
        }
        previousPointer = viewPointer;
    }
    const selectGroupItem=function(element){
        let viewPointer = element.target.dataset.pointerId;
        showSelectorView(viewPointer);
        $('[data-pointer-id="'+viewPointer+'"').addClass('selected-group-item');
        if (null!==previousGroupItem) {
            $('[data-pointer-id="'+previousGroupItem+'"').removeClass('selected-group-item');
        }
        previousGroupItem = viewPointer;
    }
    showSelectorView(0);
    $('[data-pointer-id="0"').addClass('selected-group-item');
    previousGroupItem = 0;
    $('.group-item').click(function($this){
        selectGroupItem($this);
    });
});
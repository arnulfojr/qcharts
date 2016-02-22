
function doCollapseOnReady() {

    $(document).on('click', '.toggleCollapse', function(event) {

        var content = $(this).parent().parent();
        var sibling = $(content).siblings();

        /**
         * @param domElement
         */
        function toggleElement(domElement) {
            var iconDown = "glyphicon-chevron-down";
            var iconUp = "glyphicon-chevron-up";

            if ($(domElement).hasClass(iconDown)) {
                $(domElement).removeClass(iconDown);
                $(domElement).addClass(iconUp);
                return;
            }
            $(domElement).removeClass(iconUp);
            $(domElement).addClass(iconDown);
        };

        $(sibling).animate({height: "toggle"}, 700, toggleElement(this));

    });

    $(window).resize(function() {
        $topPanel = $("#topPanels");
        $childPanels = $(".childPanel");
        $childPanels.each(function() {
            $(this).css("height", $topPanel.css("height"));
        });
    });
};

$(document).ready(doCollapseOnReady);
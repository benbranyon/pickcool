// a directive to auto-collapse long text
// in elements with the "dd-text-collapse" attribute
app.directive('ddTextCollapse', ['$compile', function($compile) {

    return {
        restrict: 'A',
        scope: true,
        link: function(scope, element, attrs) {

            // start collapsed
            scope.collapsed = false;

            // create the function to toggle the collapse
            scope.toggle = function() {
                scope.collapsed = !scope.collapsed;
            };

            // wait for changes on the text
            attrs.$observe('ddTextCollapseText', function(text) {

                // get the length from the attributes
                var maxLength = scope.$eval(attrs.ddTextCollapseMaxLength);

                if (text.length > maxLength) {
                    var converter = new Showdown.converter();
                    converter.makeHtml('#hello markdown!');
                                    
                    // split the text in two parts, the first always showing
                    var summary = converter.makeHtml(String(text).substring(0, maxLength)+'...!!!more!!!');
                    summary = summary.replace(/!!!more!!!/, ' <span class="collapse-text-toggle" ng-click="toggle()">Show More</span>');
                    var full = converter.makeHtml(text + '!!!more!!!');
                    full = full.replace(/!!!more!!!/, ' <span class="collapse-text-toggle" ng-click="toggle()">Show Less</span>');

                    // create some new html elements to hold the separate info
                    summary = $compile('<span ng-if="!collapsed">' + summary + '</span>')(scope);
                    full = $compile('<span ng-if="collapsed">' + full + '</span>')(scope);

                    element.empty();
                    element.append(summary);
                    element.append(full);
                }
                else {
                    element.empty();
                    element.append(text);
                }
            });
        }
    };
}]);
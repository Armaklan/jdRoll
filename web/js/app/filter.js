app.filter('mysqlDateToIso', function() {
  return function(badTime) {
    if(badTime != undefined) {
        var goodTime = badTime.replace(/(.+) (.+)/, "$1T$2Z");
    }
    return goodTime;
  };
});
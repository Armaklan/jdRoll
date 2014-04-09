app.filter('mysqlDateToIso', function() {
  return function(badTime) {
    var goodTime = badTime.replace(/(.+) (.+)/, "$1T$2Z");
    return goodTime;
  };
});
function main() {
  var exports = this.Spec = {};
  #include "../vendor/spec.js"

  exports = this.Newton = {};
  #include "../vendor/newton.js"

  exports = this.JSON = {};
  #include "../lib/cassandraMAP.js"

  try {
    #include "./test_cassandraMAP.js"
  } catch (exception) {
    $.bp();
    print(exception.source.split('\n')[exception.line - 1] + '\n' + exception);
  }
}

main.call({
  'load': function load(identifier, path) {
    return this[identifier];
  }
});

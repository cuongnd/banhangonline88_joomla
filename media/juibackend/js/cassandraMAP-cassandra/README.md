# cassandraMAP #

![cassandraMAP Logo](http://s21.postimg.org/qvtt7xjqf/cassandra_MAP.png)

**CassandraMAP** is a JavaScript implementation of Cassandra's Map type based on JSON3, compatible with a variety of platforms, including Internet Explorer 6, Opera 7, Safari 2, and Netscape 6. The current version is **0.1.7**.

- [Development Version](http://static.jousst.com/js/cassandraMap-0.1.7.js) *(32 KB; uncompressed with comments)*
- [Production Version](http://static.jousst.com/js/cassandraMap-0.1.7.min.js) *(8 KB; compressed)*

This code is based on [JSON 3](http://github.com/bestiejs/json3), created by [bestiejs](http://github.com/bestiejs).

[Apache Cassandra](http://cassandra.apache.org/) is an open-source distributed database managment system designed to handle large amounts of data. Within its query language, [CQL](http://www.datastax.com/documentation/cql/3.1/cql/cql_using/about_cql_c.html), there is a [map](http://www.datastax.com/documentation/cql/3.0/cql/cql_using/use_map_t.html) data type, which is equivalent to a JavaScript object. In order to query a database with CQL, a JavaScript object must be encoded into a JSON-like string that uses single quotes instead of the JSON standard double quotes. **cassandraMAP** provides a reliable, yet simple, way to encode objects into CQL-compilant map strings and decode them back to objects.  

cassandraMAP exposes two functions: `stringify()` for [serializing](https://developer.mozilla.org/en/JavaScript/Reference/Global_Objects/JSON/stringify) a JavaScript value to a CQL Map, and `parse()` for [producing](https://developer.mozilla.org/en/JavaScript/Reference/Global_Objects/JSON/parse) a JavaScript value from a CQL Map source string. Note: the links provided above are based on the JSON specification, and are provided only for reference purposes; cassandraMAP **does not** produce JSON-compilant results.

Portions of the date serialization code are adapted from the [`date-shim`](https://github.com/Yaffle/date-shim) project.

# Usage #

## Web Browsers

```html
<script src="./path/to/cassandraMap.js"></script>
<script>
  cassandraMAP.stringify({"key": "value"});
  // => "{'key':'value'}"
  cassandraMAP.parse("{'key':'value'}");
  // => {"key": "value"}
</script>
```

### Asynchronous Module Loaders

Just like JSON 3, cassandraMAP is defined as an [anonymous module](https://github.com/amdjs/amdjs-api/wiki/AMD#define-function-) for compatibility with [RequireJS](http://requirejs.org/), [`curl.js`](https://github.com/cujojs/curl), and other asynchronous module loaders.

```html
<script src="//cdnjs.cloudflare.com/ajax/libs/require.js/2.1.10/require.js"></script>
<script>
  require({
    "paths": {
      "cassandraMAP": "./path/to/cassandraMap"
    }
  }, ["cassandraMAP"], function (cassandraMAP) {
    cassandraMAP.parse("[1, 2, 3]");
    // => [1, 2, 3]
  });
</script>
```

## CommonJS Environments

```javascript
var cassandraMAP = require("./path/to/cassandraMap");
cassandraMAP.parse("[1, 2, 3]");
// => [1, 2, 3]
```
    
## Node.js
### Installation
```shell
$ npm install cassandra-map
```

### Usage
```javascript
var cassandraMAP = require("cassandra-map");
cassandraMAP.stringify({"node":"works"});
```
## JavaScript Engines

```javascript
load("./path/to/cassandraMap.js");
cassandraMAP.stringify({"Hello": 123, "Good-bye": 456}, ["Hello"], "\t");
// => "{\n\t'Hello': 123\n}"
```

## Known Incompatibilities

* Attempting to serialize the `arguments` object may produce inconsistent results across environments due to specification version differences. As a workaround, please convert the `arguments` object to an array first: `cassandraMAP.stringify([].slice.call(arguments, 0))`.

## Required Native Methods

Just like JSON 3, cassandraMAP assumes that the following methods exist and function as described in the ECMAScript specification:

- The `Number`, `String`, `Array`, `Object`, `Date`, `SyntaxError`, and `TypeError` constructors.
- `String.fromCharCode`
- `Object#toString`
- `Function#call`
- `Math.floor`
- `Number#toString`
- `Date#valueOf`
- `String.prototype`: `indexOf`, `charCodeAt`, `charAt`, `slice`.
- `Array.prototype`: `push`, `pop`, `join`.

# Contribute #

If you’d like to contribute a feature or bug fix, you can [fork](https://help.github.com/fork-a-repo/) cassandraMAP, commit your changes, and [send a pull request](https://help.github.com/send-pull-requests/). Please make sure to update the unit tests in the `test` directory as well.

Alternatively, you can use the [GitHub issue tracker](https://github.com/jcoc611/cassandraMAP/issues) to submit bug reports, feature requests, and questions, or send tweets to [@jcoc611](https://twitter.com/jcoc611).

cassandraMAP is released under the [MIT License](http://kit.mit-license.org/).

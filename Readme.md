#AnyAPI Framework

Version 0.0.1

**A framework for interaction with external data-sources.**

The AnyAPI Framework is a framework for interacting with external data-sources.
It automatically detects server capability to determine the best / most efficient method to make the connection.
For example, while a user might choose type: MySQL when initiating a query, AnyAPI will check if PHP's PDO class is available, and use it.

The AnyAPI Framework has the following methods:

##Main Methods

**Initialization**:

Creates an anyapi object and stores connection information

```php
$AnyAPI = new anyapi( $type , $credentials , $headers );
```

**$type**: Type of Query. Can be:

- GET
- POST
- PUT
- DELETE
- OPTIONS
- MySQL
- MySQLi
- COOKIE
- JSON
- WEBSOCKET
- PDO

**$credentials**: The required authentication credentials for the query.
(Can be omitted)

**$headers**: The required headers to be passed through for the query.
(Can be omitted)

**prepare**:

Prepares the query to be sent to the external data source.

```php
anyapi->prepare($query);
```

**$query**: Query to be sent to the external source.
Can be formatted as:

- Array
- Associative Array
- JSON Encoded Array
- Serialized Data String

**exec**:

Runs the query and stores the results in the anyapi object

```php
anyapi->exec();
```

**return**:

Returns the results of the query as either raw data or as parsed results.

```php
anyapi->return($format);
```

**$format**: The method the data should be formatted upon return. Can be:

- ARRAY_A (Associative Array)
- JSON_A (JSON Encodeded Array)
- XML (XML Sheet)
- CSV (CSV File)
- HTML_E (Raw Results encoded with HTML Entities)
- URL_E (Raw Results encoded as URL)
- RAW (Raw Data Return)

##Additional Methods

**destory**:

Destroys the data stored in the anyapi object, and then destorys the anyapi object itself.

```php
anyapi->destory();
```

**setOpts**:

Set options for the anyapi object. Should be called before execution.

```php
anyapi->setOpts($options);
```

**options**:

Set options for the anyapi object. Should be called before execution.

```php
anyapi->$options;
```

**debug**:

Enables logging in order to debug issues.

```php
anyapi->debug();
```

**debugLog**:

Returns an array with debug events.

```php
anyapi->debugLog;
```

##Static Methods

**canRunQueryType**:

Checks to see if the query type is supported. This is useful to check what your server and / or version of PHP supports.

```php
anyapi::canRunQueryType($type);
```

If the query type is supported, the function will return:
```php
TRUE
```

Otherwise, the function will return the name of the package / version of PHP required to run the query type.
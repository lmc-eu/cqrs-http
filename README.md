LMC CQRS Http extension
=======================

[![cqrs-types](https://img.shields.io/badge/cqrs-types-purple.svg)](https://github.com/lmc-eu/cqrs-types)
[![Latest Stable Version](https://img.shields.io/packagist/v/lmc/cqrs-http.svg)](https://packagist.org/packages/lmc/cqrs-http)
[![Tests and linting](https://github.com/lmc-eu/cqrs-http/actions/workflows/tests.yaml/badge.svg)](https://github.com/lmc-eu/cqrs-http/actions/workflows/tests.yaml)
[![Coverage Status](https://coveralls.io/repos/github/lmc-eu/cqrs-http/badge.svg?branch=main)](https://coveralls.io/github/lmc-eu/cqrs-http?branch=main)

> A library containing base implementations to help with Http Queries and Commands.
> This library is an extension for [CQRS/Bundle](https://github.com/lmc-eu/cqrs-bundle) and adds support for [PSR-7](https://www.php-fig.org/psr/psr-7/).

## Table of contents
- [Installation](#installation)
- Queries
    - [Query](#query)
        - [Abstract HTTP Query](#abstracthttpquery)
        - [Abstract HTTP GET Query](#abstracthttpgetquery)
    - [Query Handlers](#query-handlers)
- Commands
    - [Command](#command)
        - [Abstract HTTP Command](#abstracthttpcommand)
        - [Abstract HTTP DELETE Command](#abstracthttpdeletecommand)
        - [Abstract HTTP PATCH Command](#abstracthttppatchcommand)
        - [Abstract HTTP POST Command](#abstracthttppostcommand)
        - [Abstract HTTP PUT Command](#abstracthttpputcommand)
    - [Send Command Handlers](#send-command-handlers)
- [Response Decoders](#response-decoders)
- [Profiler Formatters](#profiler-formatters)

## Installation
```shell
composer require lmc/cqrs-http
```

**NOTE**: You will also need an implementation for [PSR-7](https://packagist.org/providers/psr/http-message-implementation), [PSR-17](https://packagist.org/providers/psr/http-factory-implementation) and [PSR-18](https://packagist.org/providers/psr/http-client-implementation) for HTTP extensions to work.

## Query
Query is a request which fetch a data without changing anything. [See more here](https://github.com/lmc-eu/cqrs-types#query-interface)

### AbstractHttpQuery
A base HTTP Query, it abstracts a creating of a `Psr\Http\Message\RequestInterface` (PSR-7) using a `Psr\Http\Message\RequestFactoryInterface` (PSR-17) (*it is directly required to be injected into a query*).

It also implements a `ProfileableInterface` feature.

| Method | Type | Description |
| ---    | ---  | ---         |
| `getRequestType` | **final** | It declares a request type of a Http query to be a `Psr\Http\Message\RequestInterface` |
| `getHttpMethod` | abstract | It requires a query to return a http method of a query. (see [PSR Http Message Util](https://github.com/php-fig/http-message-util)) |
| `getUri` | abstract | This method returns a URI of the Query - it may be just a string or a `Psr\Http\Message\UriInterface` (PSR-7) instance. |
| `modifyRequest` | *base* | If you overwrite this method, you can manipulate a `RequestInterface` instance. |
| `getProfilerId` | *base* | It's a predefined creating of profiler id for a http query. It creates a profiler id based on `http method` and `uri`. |
| `getProfilerData` | *base* | If you overwrite this method, you can specify additional profiler data. Default is `null` (*no data*). |
| `__toString` | *base* | It's a predefined casting a Query into string, it returns a string representation of `uri`. |

### AbstractHttpGetQuery
A base HTTP **GET** Query, it abstracts a creating of a `Psr\Http\Message\RequestInterface` (PSR-7) using a `Psr\Http\Message\RequestFactoryInterface` (PSR-17) (*it is directly required to be injected into a query*).

It extends a base `AbstractHttpQuery` and predefine some abstract methods. It also adds `CacheableInterface` feature, since a GET request is mostly cacheable

| Method | Type | Description |
| ---    | ---  | ---         |
| `getHttpMethod` | **final** | It declares a http method of this query to be `GET`. |
| `getUri` | abstract | This method returns a URI of the Query - it may be just a `string` or a `Psr\Http\Message\UriInterface` (PSR-7) instance. |
| `getCacheTime` | *base* | It returns a default value for a cache time of 30 minutes. |
| `getCacheKey` | *base* | It creates a `CacheKey` out of a static class name (*your implementation class name*) and a `uri`, which should create a unique enough cache key for most queries. |

**TIP**: If you want to use this implementation but don't need a cache, you can simply return a `CacheTime::noCache()` in your implementation of `getCacheTime` method.

## Query Handlers
It is responsible for handling a specific Query request and passing a result into `OnSuccess` callback. [See more here](https://github.com/lmc-eu/cqrs-types#query-handler-interface).

### Http Query Handler
This handler supports `Psr\Http\Message\RequestInterface` and handles it into `Psr\Http\Message\ResponseInterface`.

It also checks a status code of a response and marks it as error if it is an error code:
- 400 -> `HttpBadRequestException`
- 500 -> `HttpServerErrorException`

---

## Command
Command is a request which change a data and may return result data. [See more here](https://github.com/lmc-eu/cqrs-types#command-interface)

### AbstractHttpCommand
A base HTTP Command, it abstracts a creating of a `Psr\Http\Message\RequestInterface` (PSR-7) using a `Psr\Http\Message\RequestFactoryInterface` (PSR-17) (*it is directly required to be injected into a command*).

It also implements a `ProfileableInterface` feature.

| Method | Type | Description |
| ---    | ---  | ---         |
| `getRequestType` | **final** | It declares a request type of a Http command to be a `Psr\Http\Message\RequestInterface` |
| `getHttpMethod` | abstract | It requires a command to return a http method of a command. (see [PSR Http Message Util](https://github.com/php-fig/http-message-util)) |
| `getUri` | abstract | This method returns a URI of the Command - it may be just a `string` or a `Psr\Http\Message\UriInterface` (PSR-7) instance. |
| `modifyRequest` | *base* | If you overwrite this method, you can manipulate a `RequestInterface` instance. |
| `getProfilerId` | *base* | It's a predefined creating of profiler id for a http command. It creates a profiler id based on `http method` and `uri`. |
| `getProfilerData` | *base* | If you overwrite this method, you can specify additional profiler data. Default is `null` (*no data*). |
| `__toString` | *base* | It's a predefined casting a Command into string, it returns a string representation of `uri`. |

### AbstractHttpDeleteCommand
A base HTTP **DELETE** Command, it abstracts a creating of a `Psr\Http\Message\RequestInterface` (PSR-7) using a `Psr\Http\Message\RequestFactoryInterface` (PSR-17) (*it is directly required to be injected into a command*).

It extends a base `AbstractHttpCommand` and predefine some abstract methods.

| Method | Type | Description |
| ---    | ---  | ---         |
| `getHttpMethod` | **final** | It declares a http method of this query to be `DELETE`. |

### AbstractHttpPatchCommand
A base HTTP **PATCH** Command, it abstracts a creating of a `Psr\Http\Message\RequestInterface` (PSR-7) using a `Psr\Http\Message\RequestFactoryInterface` (PSR-17) (*it is directly required to be injected into a command*).

It extends a base `AbstractHttpCommand` and predefine some abstract methods.

| Method | Type | Description |
| ---    | ---  | ---         |
| `getHttpMethod` | **final** | It declares a http method of this query to be `PATCH`. |

### AbstractHttpPostCommand
A base HTTP **POST** Command, it abstracts a creating of a `Psr\Http\Message\RequestInterface` (PSR-7) using a `Psr\Http\Message\RequestFactoryInterface` (PSR-17) (*it is directly required to be injected into a command*).

It extends a base `AbstractHttpCommand` and predefine some abstract methods.

| Method | Type | Description |
| ---    | ---  | ---         |
| `getHttpMethod` | **final** | It declares a http method of this query to be `POST`. |
| `createBody` | abstract | It requires a command to return an instance of `Psr\Http\Message\StreamInterface` which is used as a POST request body. |
| `createRequest` | *base* | It creates a request with a body. |
| `getProfilerData` | *base* | It adds a `Body` into additional data for profiler, so it may be shown later in profiler. |

### AbstractHttpPutCommand
A base HTTP **PATCH** Command, it abstracts a creating of a `Psr\Http\Message\RequestInterface` (PSR-7) using a `Psr\Http\Message\RequestFactoryInterface` (PSR-17) (*it is directly required to be injected into a command*).

It extends a base `AbstractHttpCommand` and predefine some abstract methods.

| Method | Type | Description |
| ---    | ---  | ---         |
| `getHttpMethod` | **final** | It declares a http method of this query to be `PATCH`. |

## Send Command Handlers
It is responsible for handling a specific Command request and passing a result into `OnSuccess` callback. [See more here](https://github.com/lmc-eu/cqrs-types#send-command-handler-interface).

### Http Send Command Handler
This handler supports `Psr\Http\Message\RequestInterface` and handles it into `Psr\Http\Message\ResponseInterface`.

It also checks a status code of a response and marks it as error if it is an error code:
- 400 -> `HttpBadRequestException`
- 500 -> `HttpServerErrorException`

---

## Response Decoders
It is meant to decode a response (a result of either `QueryHandlerInterface` or a `SendCommandHandlerInterface`). [See more here](https://github.com/lmc-eu/cqrs-types#response-decoder-interface).

### HttpMessageResponseDecoder
It decodes a `Psr\Http\Message\ResponseInterface` into a `Psr\Http\Message\StreamInterface` by getting a body of a response.

### StreamResponseDecoder
It decodes a `Psr\Http\Message\StreamInterface` into a `string` by getting a stream contents (*if possible*).

**Note**: There is also a [JsonResponseDecoder](https://github.com/lmc-eu/cqrs-types#jsonresponsedecoder) which decodes a string into an array.

## Profiler Formatters

### HttpProfilerFormatter
It formats a `Psr\Http\Message\MessageInterface` and `Psr\Http\Message\StreamInterface` into a readable format, so a data is nicer in profiler.

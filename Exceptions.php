<?php
namespace Skel;

class ApplicationPermissionsException extends \RuntimeException {}
class DataValidationException extends \RuntimeException {}
class Http404Exception extends \RuntimeException {}
class UnauthenticatedUserException extends \RuntimeException { }
class UnauthorizedFunctionAccessException extends \RuntimeException { }
class UndefinedActionException extends \RuntimeException {}
class NonexistentFileException extends \RuntimeException {}

?>

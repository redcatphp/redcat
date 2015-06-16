<?php
namespace InterEthnic\Punic;

/**
 * An exception raised by and associated to Punic
 */
class Exception extends \Exception
{
    /**
     * Exception code for the \InterEthnic\Punic\Exception\NotImplemented exception
     * @var int
     */
    const NOT_IMPLEMENTED = 10000;

    /**
     * Exception code for the \InterEthnic\Punic\Exception\InvalidLocale exception
     * @var int
     */
    const INVALID_LOCALE = 10001;

    /**
     * Exception code for the \InterEthnic\Punic\Exception\InvalidDataFile exception
     * @var int
     */
    const INVALID_DATAFILE = 10002;

    /**
     * Exception code for the \InterEthnic\Punic\Exception\DataFolderNotFound exception
     * @var int
     */
    const DATA_FOLDER_NOT_FOUND = 10003;

    /**
     * Exception code for the \InterEthnic\Punic\Exception\DataFileNotFound exception
     * @var int
     */
    const DATA_FILE_NOT_FOUND = 10004;

    /**
     * Exception code for the \InterEthnic\Punic\Exception\DataFileNotReadable exception
     * @var int
     */
    const DATA_FILE_NOT_READABLE = 10005;

    /**
     * Exception code for the \InterEthnic\Punic\Exception\BadDataFileContents exception
     * @var int
     */
    const BAD_DATA_FILE_CONTENTS = 10006;

    /**
     * Exception code for the \InterEthnic\Punic\Exception\BadArgumentType exception
     * @var int
     */
    const BAD_ARGUMENT_TYPE = 10007;

    /**
     * Exception code for the \InterEthnic\Punic\Exception\ValueNotInList exception
     * @var int
     */
    const VALUE_NOT_IN_LIST = 10008;

    /**
     * Initializes the instance
     * @param string $message
     * @param int $code
     * @param \Exception $previous The previous exception used for the exception chaining
     */
    public function __construct($message, $code = null, $previous = null)
    {
        parent::__construct($message, $code ? $code : 1, $previous);
    }
}

<?php

namespace QCharts\CoreBundle\Exception\Messages;


class ExceptionMessage
{
    const ENTITY_NOT_FOUND = "No matches were found in the query";

    const INSTANCE_NOT_FOUND = "No instance found";

    const DIRECTORY_NOT_FOUND_TEXT = "The give directory is not valid";

    const NAME_OVERLAPPING_TEXT = "The name already exists in the same context";

    const DIRECTORY_NOT_EMPTY_TEXT = "The given directory is not empty, please move or delete the files under it";

    const TYPE_NOT_VALID_TEXT = "The passed argument type is not valid";

    const FILE_NOT_READABLE = "It was not possible to read the file";

    const FILE_NOT_WRITABLE = "It was not possible to write in the file";

    const DEPENDENCY_NOT_AVAILABLE_TEXT = "The given dependency was not passed";

    const QUERY_IS_NOT_FETCHING = "The requested query does not support Snapshot or Time Machine";

    /**
     * @param $description
     * @return string
     */
    static public function DEPENDENCY_NOT_AVAILABLE($description)
    {
        return ExceptionMessage::DEPENDENCY_NOT_AVAILABLE_TEXT."{$description}";
    }

    /**
     * @param $extra
     * @return string
     */
    static public function TYPE_NOT_VALID($extra)
    {
        return ExceptionMessage::TYPE_NOT_VALID_TEXT.", {$extra}";
    }

    /**
     * @param $extra
     * @return string
     */
    static public function DIRECTORY_NOT_EMPTY($extra)
    {
        return ExceptionMessage::DIRECTORY_NOT_EMPTY_TEXT.", {$extra}";
    }

    /**
     * @param $description
     * @return string
     */
    static public function ENTITY_NOT_FOUND($description)
    {
        return "No matches were found in the query, {$description}";
    }

    /**
     * @param $description
     * @return string
     */
    static public function INSTANCE_NOT_FOUND($description)
    {
        return "No instance found, {$description}";
    }

    /**
     * @param $description
     * @return string
     */
    static public function DIRECTORY_NOT_FOUND($description)
    {
        return "The given directory was not found, {$description}";
    }

    /**
     * @param $description
     * @return string
     */
    static public function NAME_OVERLAPPING($description)
    {
        return ExceptionMessage::NAME_OVERLAPPING_TEXT.", {$description}";
    }

}
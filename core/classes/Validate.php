<?php


class Validate
{
    /**
     * Check for e-mail validity
     *
     * @param string $email e-mail address to validate
     * @return boolean Validity is ok or not
     */
    static public function isEmail($email)
    {
        return preg_match('/^[a-z0-9!#$%&\'*+\/=?^`{}|~_-]+[.a-z0-9!#$%&\'*+\/=?^`{}|~_-]*@[a-z0-9]+[._a-z0-9-]*\.[a-z0-9]+$/ui', $email);
    }


    /**
     * Check for password validity
     *
     * @param string $passwd Password to validate
     * @return boolean Validity is ok or not
     */
    static public function isPasswd($passwd, $size = 5)
    {
        return preg_match('/^[.a-z_0-9-]{'.$size.',32}$/ui', $passwd);
    }

    /**
     * Check for table or identifier validity
     * Mostly used in database for table names and id_table
     *
     * @param string $table Table/identifier to validate
     * @return boolean Validity is ok or not
     */
    static public function isTableOrIdentifier($table)
    {
        return preg_match('/^[a-z0-9_-]+$/ui', $table);
    }

    /**
     * Check object validity
     *
     * @param integer $object Object to validate
     * @return boolean Validity is ok or not
     */
    static public function isLoadedObject($object)
    {
        return is_object($object) && $object->id;
    }

}


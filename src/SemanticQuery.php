<?php
namespace Light\Wechat;

class SemanticQuery
{
    private $_query = [];

    public function setQuery($string)
    {
        $this->_query['query'] = $string;
        return $this;
    }

    public function setCity($city)
    {
        $this->_query['city'] = $city;
        return $this;
    }

    public function setRegion($region)
    {
        $this->_query['region'] = $region;
        return $this;
    }

    public function setCategory($category)
    {
        $category = (array) $category;
        var_dump($category);
        $this->_query['category'] = implode(',', $category);
    }

    public function setAppid($app_id)
    {
        $this->_query['appid'] = $app_id;
        return $this;
    }

    public function setUid($uid)
    {
        $this->_query['uid'] = $uid;
        return $this;
    }

    public function setLatitude($latitude)
    {
        $this->_query['latitude'] = $latitude;
        return $this;
    }

    public function setLongitude($longitude)
    {
        $this->_query['longitude'] = $longitude;
        return $this;
    }

    public function getQuery()
    {
        return $this->_query;
    }

    //setter and getter things

    public function __get($name)
    {
        $getter = 'get' . $name;
        if (method_exists($this, $getter)) {
            return $this->$getter();
        } elseif (method_exists($this, 'set' . $name)) {
            throw new InvalidCallException('Getting write-only property:' . get_class($this) . '::' . $name);
        } else {
            throw new UnknownPropertyException('Getting unknow property:' . get_class($this) . '::' . $name);
        }
    }

    public function __set($name, $value)
    {
        $setter = 'set' . $name;

        if (method_exists($this, $setter)) {
            return $this->$setter($value);
        } elseif (method_exists($this, 'get' . $name)) {
            throw new InvalidCallException('Setting read-only property: ' . get_class($this) . '::' . $name);
        } else {
            throw new UnknownPropertyException('Setting unknown property: ' . get_class($this) . '::' . $name);
        }
    }

    public function __isset($name)
    {
        $getter = 'get' . $name;
        if (method_exists($this, $getter)) {
            return $this->$getter() !== null;
        } else {
            return false;
        }
    }

    public function __unset($name)
    {
        $setter = 'set' . $name;
        if (method_exists($this, $setter)) {
            $this->$setter(null);
        } elseif (method_exists($this, 'get' . $name)) {
            throw new InvalidCallException('Unsetting read-only property: ' . get_class($this) . '::' . $name);
        }
    }

    public function __call($name, $params)
    {
        throw new UnknownMethodException('Calling unknown method: ' . get_class($this) . "::$name()");
    }

    public function hasMethod($name)
    {
        return method_exists($this, $name);
    }

    public function __toString()
    {
        return json_encode($this->_query);
    }
}

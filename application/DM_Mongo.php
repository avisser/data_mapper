<?php

class DM_Mongo
{
    const DM_DATABASE = 'data_mapper';

    const DM_WORKSHEET_COLLECTION = 'worksheet';
    const DM_PHOTOS_COLLECTION = 'photos';
    const DM_UPLOADS_COLLECTION = 'uploads';


    /**
     * @static
     * @return Mongo
     */
    public static function instance()
    {
        $mongo_config_data = $GLOBALS['_CONFIG']['mongo'];
        $connection_string = $mongo_config_data['adapter'].'://'.implode(',',$mongo_config_data['host']);
        $options = isset($mongo_config_data['replicaSet']) ? array( "replicaSet" => $mongo_config_data['replicaSet'] ) : array();
        $options["username"] = $mongo_config_data['username'];
        $options["password"] = $mongo_config_data['password'];

        return new MongoClient(
            $connection_string,
            $options
        );
    }

    /**
     * @param $database
     * @param $collection
     * @return MongoCollection
     */
    public static function collection($database,$collection)
    {
        $mongo = self::instance();
        $db = $mongo->$database;
        return $db->$collection ;
    }

    /**
     * @param $collection
     * @param $query
     * @param array $projection,
     * @param string $database
     * @return array|null
     */
    public static function findOne($collection, $query, $projection = array(), $database = 'data_mapper')
    {
        $collection = self::collection($database,$collection);

        return $collection->findOne($query,$projection);
    }

    /**
     * @param $collection
     * @param $query
     * @param array $projection
     * @param string $database
     * @return MongoCursor
     */
    public static function find($collection, $query, $projection = array(), $database = 'data_mapper')
    {
        $collection = self::collection($database,$collection);
        return $collection->find($query,$projection);
    }

    /**
     * @param $collection
     * @param $criteria
     * @param $new_data
     *  @param string $database
     * @return bool
     */
    public static function update($collection, $criteria, $new_data, $database = 'data_mapper')
    {
        $collection = self::collection($database,$collection);
        return $collection->update($criteria,$new_data);
    }

    /**
     * @param $collection
     * @param $data
     * @param string $database
     * @return MongoId
     */
    public static function insert($collection, $data, $database = 'data_mapper')
    {
        $collection = self::collection($database,$collection);
        $collection->insert($data);
        return $data['_id'];
    }

    /**
     * @param $collection
     * @param $query
     * @param string $database
     */
    public static function remove($collection, $query, $database = 'data_mapper')
    {
        $collection = self::collection($database,$collection);
        $collection->remove($query);
    }
}


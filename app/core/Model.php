<?php
/**
 * Base Model Class
 * All models inherit from this class for data access
 */

class Model {
    /**
     * @var array Sample data storage (can be replaced with database)
     */
    protected static $data = [];
    
    /**
     * Get all records
     */
    public static function getAll() {
        return static::$data;
    }
    
    /**
     * Get record by ID
     */
    public static function getById($id) {
        foreach (static::$data as $record) {
            if (isset($record['id']) && $record['id'] == $id) {
                return $record;
            }
        }
        return null;
    }
    
    /**
     * Get records by field value
     */
    public static function getBy($field, $value) {
        $results = [];
        foreach (static::$data as $record) {
            if (isset($record[$field]) && $record[$field] == $value) {
                $results[] = $record;
            }
        }
        return $results;
    }
    
    /**
     * Find records (basic search)
     */
    public static function find($criteria) {
        $results = [];
        foreach (static::$data as $record) {
            $match = true;
            foreach ($criteria as $field => $value) {
                if (!isset($record[$field]) || $record[$field] != $value) {
                    $match = false;
                    break;
                }
            }
            if ($match) {
                $results[] = $record;
            }
        }
        return $results;
    }
    
    /**
     * Create new record
     */
    public static function create($data) {
        // Generate ID if not provided
        if (!isset($data['id'])) {
            $data['id'] = count(static::$data) + 1;
        }
        
        static::$data[] = $data;
        return $data;
    }
    
    /**
     * Update record
     */
    public static function update($id, $data) {
        foreach (static::$data as &$record) {
            if (isset($record['id']) && $record['id'] == $id) {
                $record = array_merge($record, $data);
                return $record;
            }
        }
        return null;
    }
    
    /**
     * Delete record
     */
    public static function delete($id) {
        foreach (static::$data as $key => $record) {
            if (isset($record['id']) && $record['id'] == $id) {
                unset(static::$data[$key]);
                return true;
            }
        }
        return false;
    }
    
    /**
     * Get total count
     */
    public static function count() {
        return count(static::$data);
    }
    
    /**
     * Paginate records
     */
    public static function paginate($page = 1, $perPage = 10) {
        $offset = ($page - 1) * $perPage;
        $items = array_slice(static::$data, $offset, $perPage);
        $total = count(static::$data);
        $pages = ceil($total / $perPage);
        
        return [
            'items' => $items,
            'page' => $page,
            'perPage' => $perPage,
            'total' => $total,
            'pages' => $pages
        ];
    }
}

?>

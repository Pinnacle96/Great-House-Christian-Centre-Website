<?php
namespace App\Core;

class Model {
    protected $db;
    protected $table;

    /**
     * When true, update() and delete() are automatically restricted to the
     * current user's branch (defense-in-depth against cross-branch IDOR).
     * Superadmins are never restricted; a branch user with no branch is
     * fail-closed (matches nothing).
     */
    protected $branchScoped = false;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Returns [sqlFragment, branchId] for the current user, using a named
     * placeholder so it can be merged with the base methods' named params.
     */
    protected function branchCondition() {
        if (BranchScope::isSuperAdmin()) {
            return ['', null];
        }

        $branchId = BranchScope::currentBranchId();
        if (!$branchId) {
            return ['1 = 0', null];
        }

        return ['branch_id = :branch_scope_id', $branchId];
    }

    public function findAll() {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table}");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function find($id) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function where($column, $value) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$column} = :value");
        $stmt->execute(['value' => $value]);
        return $stmt->fetchAll();
    }
    
    public function first($column, $value) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$column} = :value LIMIT 1");
        $stmt->execute(['value' => $value]);
        return $stmt->fetch();
    }

    public function delete($id) {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $params = ['id' => $id];

        if ($this->branchScoped) {
            [$condition, $branchId] = $this->branchCondition();
            if ($condition !== '') {
                $sql .= " AND {$condition}";
                if ($branchId !== null) {
                    $params['branch_scope_id'] = $branchId;
                }
            }
        }

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }
    
    public function create($data) {
        $keys = array_keys($data);
        $fields = implode(', ', $keys);
        $placeholders = ':' . implode(', :', $keys);
        
        $sql = "INSERT INTO {$this->table} ({$fields}) VALUES ({$placeholders})";
        $stmt = $this->db->prepare($sql);
        
        if($stmt->execute($data)) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    public function update($id, $data) {
        $fields = '';
        foreach(array_keys($data) as $key) {
            $fields .= "{$key} = :{$key}, ";
        }
        $fields = rtrim($fields, ', ');

        $data['id'] = $id;

        $sql = "UPDATE {$this->table} SET {$fields} WHERE id = :id";

        if ($this->branchScoped) {
            [$condition, $branchId] = $this->branchCondition();
            if ($condition !== '') {
                $sql .= " AND {$condition}";
                if ($branchId !== null) {
                    $data['branch_scope_id'] = $branchId;
                }
            }
        }

        $stmt = $this->db->prepare($sql);

        return $stmt->execute($data);
    }
}

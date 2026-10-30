<?php

class SearchHistory {
    private PDO $con;

    public function __construct(PDO $con) {
        $this->con = $con;
    }

    public function addSearch($user_id, $searchTerm, $resultCount = 0) {
        $term = trim($searchTerm);

        if (empty($term) || empty($user_id) || (int)$user_id <= 0) {
            return false;
        }

        try {
            $deleteSql = "DELETE FROM search_history WHERE user_id = :user_id AND LOWER(search_term) = LOWER(:term)";
            $deleteStmt = $this->con->prepare($deleteSql);
            $deleteStmt->execute([
                ":user_id" => (int)$user_id, 
                ":term"    => $term
            ]);

            $sql = "INSERT INTO search_history (user_id, search_term, results_count, searched_at) 
                    VALUES (:user_id, :term, :resultCount, NOW())";
            $stmt = $this->con->prepare($sql);
            
            return $stmt->execute([
                ":user_id"     => (int)$user_id,
                ":term"        => $term,
                ":resultCount" => (int)$resultCount
            ]);
        } catch (PDOException $e) {
            error_log("SearchHistory addSearch Error: " . $e->getMessage());
            return false;
        }
    }

    public function getRecentSearches($user_id, $limit = 5) {
        if (empty($user_id) || (int)$user_id <= 0) {
            return [];
        }

        try {
            $limit = (int)$limit;
            $sql = "SELECT search_id, search_term 
                    FROM search_history 
                    WHERE user_id = :user_id 
                    ORDER BY searched_at DESC 
                    LIMIT $limit";

            $stmt = $this->con->prepare($sql);
            $stmt->execute([":user_id" => (int)$user_id]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("SearchHistory getRecentSearches Error: " . $e->getMessage());
            return [];
        }
    }

    public function deleteSearchItem($user_id, $search_id) {
        if (empty($user_id) || (int)$user_id <= 0 || empty($search_id) || (int)$search_id <= 0) {
            return false;
        }

        try {
            $sql = "DELETE FROM search_history 
                    WHERE search_id = :search_id AND user_id = :user_id";

            $stmt = $this->con->prepare($sql);
            return $stmt->execute([
                ':search_id' => (int)$search_id,
                ':user_id'   => (int)$user_id
            ]);
        } catch (PDOException $e) {
            error_log("SearchHistory deleteSearchItem Error: " . $e->getMessage());
            return false;
        }
    }

    public function clearAllHistory($user_id) {
        if (empty($user_id) || (int)$user_id <= 0) {
            return false;
        }

        try {
            $sql = "DELETE FROM search_history WHERE user_id = :user_id";
            $stmt = $this->con->prepare($sql);

            return $stmt->execute([
                ':user_id' => (int)$user_id
            ]);
        } catch (PDOException $e) {
            error_log("SearchHistory clearAllHistory Error: " . $e->getMessage());
            return false;
        }
    }
}
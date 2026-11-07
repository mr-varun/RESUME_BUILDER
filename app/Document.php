<?php
require_once ROOT_PATH . '/config/database.php';
require_once ROOT_PATH . '/app/Helpers.php';

class Document {
    public static function create(int $userId, string $docType = 'resume', string $title = 'Untitled'): int {
        $data = json_encode(Helpers::emptyDocData());
        DB::exec(
            'INSERT INTO documents (user_id, doc_type, title, data) VALUES (?, ?, ?, ?)',
            [$userId, $docType, $title, $data]
        );
        return (int) DB::lastId();
    }

    public static function createGuest(string $docType = 'biodata', string $title = 'Quick Biodata'): int {
        $data = json_encode(Helpers::emptyDocData());
        DB::exec(
            'INSERT INTO documents (user_id, doc_type, title, data) VALUES (NULL, ?, ?, ?)',
            [$docType, $title, $data]
        );
        return (int) DB::lastId();
    }

    public static function get(int $id): ?array {
        $doc = DB::row('SELECT * FROM documents WHERE id = ?', [$id]);
        if (!$doc) return null;
        $doc['data'] = json_decode($doc['data'] ?? '{}', true) ?: [];
        $doc['data'] = Helpers::mergeDocData($doc['data']);
        return $doc;
    }

    public static function getForUser(int $id, int $userId): ?array {
        $doc = DB::row('SELECT * FROM documents WHERE id = ? AND user_id = ?', [$id, $userId]);
        if (!$doc) return null;
        $doc['data'] = json_decode($doc['data'] ?? '{}', true) ?: [];
        $doc['data'] = Helpers::mergeDocData($doc['data']);
        return $doc;
    }

    public static function getBySlug(string $slug): ?array {
        $doc = DB::row(
            'SELECT * FROM documents WHERE public_slug = ? AND public_enabled = 1',
            [$slug]
        );
        if (!$doc) return null;
        // Check expiry
        if ($doc['link_expiry'] && strtotime($doc['link_expiry']) < time()) return null;
        $doc['data'] = json_decode($doc['data'] ?? '{}', true) ?: [];
        $doc['data'] = Helpers::mergeDocData($doc['data']);
        return $doc;
    }

    public static function userDocs(int $userId, string $search = '', string $type = '', string $status = ''): array {
        $where  = ['d.user_id = ?'];
        $params = [$userId];
        if ($search) { $where[] = 'd.title LIKE ?'; $params[] = '%' . $search . '%'; }
        if ($type)   { $where[] = 'd.doc_type = ?'; $params[] = $type; }
        if ($status) { $where[] = 'd.status = ?';   $params[] = $status; }
        $sql = 'SELECT d.* FROM documents d WHERE ' . implode(' AND ', $where) . ' ORDER BY d.updated_at DESC';
        return DB::rows($sql, $params);
    }

    public static function save(int $id, int $userId, array $data, array $meta = []): bool {
        $jsonData = json_encode($data, JSON_UNESCAPED_UNICODE);
        $sets  = ['data = ?', 'updated_at = NOW()'];
        $params = [$jsonData];
        if (isset($meta['title']))    { $sets[] = 'title = ?';    $params[] = substr(trim($meta['title']), 0, 200); }
        if (isset($meta['doc_type'])) { $sets[] = 'doc_type = ?'; $params[] = $meta['doc_type']; }
        if (isset($meta['status']))   { $sets[] = 'status = ?';   $params[] = $meta['status']; }
        $params[] = $id;
        $params[] = $userId;
        $rows = DB::exec('UPDATE documents SET ' . implode(', ', $sets) .
                         ' WHERE id = ? AND user_id = ?', $params);
        return $rows > 0;
    }

    public static function updateMeta(int $id, int $userId, array $fields): bool {
        $allowed = ['title', 'status', 'doc_type', 'public_enabled', 'public_slug', 'link_expiry'];
        $sets = []; $params = [];
        foreach ($fields as $k => $v) {
            if (in_array($k, $allowed, true)) { $sets[] = "$k = ?"; $params[] = $v; }
        }
        if (!$sets) return false;
        $params[] = $id; $params[] = $userId;
        return DB::exec('UPDATE documents SET ' . implode(', ', $sets) .
                        ' WHERE id = ? AND user_id = ?', $params) > 0;
    }

    public static function delete(int $id, int $userId): bool {
        return DB::exec('DELETE FROM documents WHERE id = ? AND user_id = ?', [$id, $userId]) > 0;
    }

    public static function duplicate(int $id, int $userId): ?int {
        $doc = self::getForUser($id, $userId);
        if (!$doc) return null;
        $newTitle = 'Copy of ' . $doc['title'];
        DB::exec(
            'INSERT INTO documents (user_id, doc_type, title, data) VALUES (?, ?, ?, ?)',
            [$userId, $doc['doc_type'], $newTitle, json_encode($doc['data'], JSON_UNESCAPED_UNICODE)]
        );
        return (int) DB::lastId();
    }

    public static function incrementView(int $id): void {
        DB::exec('UPDATE documents SET view_count = view_count + 1 WHERE id = ?', [$id]);
    }

    public static function enablePublic(int $id, int $userId, ?string $expiry = null): string {
        $slug = Helpers::uniqueSlug();
        $fields = ['public_enabled' => 1, 'public_slug' => $slug];
        if ($expiry) $fields['link_expiry'] = $expiry;
        self::updateMeta($id, $userId, $fields);
        return $slug;
    }

    public static function disablePublic(int $id, int $userId): void {
        self::updateMeta($id, $userId, ['public_enabled' => 0]);
    }
}

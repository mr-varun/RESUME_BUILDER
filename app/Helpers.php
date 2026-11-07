<?php
class Helpers {
    public static function e(mixed $val): string {
        return htmlspecialchars((string)$val, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    public static function generateSlug(int $length = SLUG_LENGTH): string {
        $chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
        $slug  = '';
        for ($i = 0; $i < $length; $i++) {
            $slug .= $chars[random_int(0, strlen($chars) - 1)];
        }
        return $slug;
    }

    public static function uniqueSlug(): string {
        do {
            $slug = self::generateSlug();
            $exists = DB::row('SELECT id FROM documents WHERE public_slug = ?', [$slug]);
        } while ($exists);
        return $slug;
    }

    public static function redirect(string $url): void {
        header('Location: ' . $url);
        exit;
    }

    public static function jsonResponse(array $data, int $status = 200): void {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        exit;
    }

    public static function sanitizeFilename(string $name): string {
        $name = preg_replace('/[^a-z0-9_\-\.]/i', '_', $name);
        return substr($name, 0, 100);
    }

    public static function timeAgo(string $datetime): string {
        $diff = time() - strtotime($datetime);
        if ($diff < 60)     return 'just now';
        if ($diff < 3600)   return floor($diff / 60) . 'm ago';
        if ($diff < 86400)  return floor($diff / 3600) . 'h ago';
        if ($diff < 604800) return floor($diff / 86400) . 'd ago';
        return date('d M Y', strtotime($datetime));
    }

    public static function formatDate(string $date, string $format = 'd M Y'): string {
        if (empty($date)) return '';
        return date($format, strtotime($date));
    }

    public static function emptyDocData(): array {
        return [
            'basic' => [
                'full_name' => '', 'email' => '', 'phone' => '',
                'address' => '', 'city' => '', 'state' => '', 'country' => 'India',
                'photo' => '', 'linkedin' => '', 'github' => '',
                'website' => '', 'objective' => '',
            ],
            'education'   => [],
            'experience'  => [],
            'skills'      => [],
            'personal'    => [
                'dob' => '', 'age' => '', 'gender' => '',
                'marital_status' => '', 'nationality' => 'Indian',
                'religion' => '', 'caste' => '',
                'father_name' => '', 'mother_name' => '',
                'languages' => '', 'hobbies' => '',
                'height' => '', 'weight' => '',
                // Marriage biodata specific fields
                'birth_time'        => '',
                'birth_place'       => '',
                'rashi'             => '',
                'nakshtra'          => '',
                'gotra'             => '',
                'manglik'           => '',
                'occupation'        => '',
                'annual_income'     => '',
                'land'              => '',
                'brothers'          => '',
                'sisters'           => '',
                'father_occupation' => '',
                'mother_occupation' => '',
                'uncle_name'        => '',
                'uncle_occupation'  => '',
                'family_type'       => '',
                'family_status'     => '',
                'god_left'          => '|| Shree Ganesh ||',
                'god_right'         => '|| Shree Devi Prasanna ||',
                'god_left_img'      => '',
                'god_right_img'     => '',
                'contact_person'    => '',
                'contact_address'   => '',
                'taluka'            => '',
                'district'          => '',
            ],
            'extras' => [
                'certifications' => [],
                'projects'       => [],
                'achievements'   => [],
                'references'     => [],
            ],
            'styling' => [
                'template'    => 'indian',
                'accent_color'=> '#1a6b6b',
                'font_size'   => 14,
                'font_family' => 'Times New Roman',
                'sections_visible' => [
                    'objective'      => true,
                    'education'      => true,
                    'experience'     => true,
                    'skills'         => true,
                    'personal'       => true,
                    'certifications' => true,
                    'projects'       => true,
                    'achievements'   => true,
                    'references'     => true,
                ],
            ],
        ];
    }

    public static function mergeDocData(array $saved): array {
        return array_replace_recursive(self::emptyDocData(), $saved);
    }

    public static function qrUrl(string $url): string {
        return 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode($url);
    }
}

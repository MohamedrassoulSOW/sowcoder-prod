<?php

declare(strict_types=1);

function content_defaults(): array
{
    $defaults = require __DIR__ . '/data/content.php';

    $defaults['settings'] = [
        'site_name' => 'SowCoder',
        'tagline' => 'Solutions digitales pour votre croissance',
        'email' => 'contact@sowcoder.com',
        'address' => 'Sangalkam, Dakar, Sénégal',
        'meta_description' => 'SowCoder — agence digitale à Dakar. Développement web, marketing, design, formations et solutions pour entreprises.',
        'phones' => [
            ['label' => 'Sénégal', 'number' => '+221 77 790 14 60', 'tel' => '+221777901460'],
            ['label' => 'Maroc', 'number' => '+212 684 088765', 'tel' => '+212684088765'],
        ],
        'whatsapp' => [
            ['label' => 'WhatsApp Sénégal', 'url' => 'https://wa.me/221777901460'],
            ['label' => 'WhatsApp Maroc', 'url' => 'https://wa.me/212684088765'],
        ],
    ];

    return $defaults;
}

function content_ensure_tables(): void
{
    static $ready = false;
    if ($ready) {
        return;
    }

    $sqlFile = dirname(__DIR__) . '/database/site_tables.sql';
    if (is_file($sqlFile)) {
        $sql = (string) file_get_contents($sqlFile);
        // Retirer USE ... pour exécuter via PDO déjà connecté à sowcoder
        $sql = preg_replace('/^\s*USE\s+`?sowcoder`?\s*;/mi', '', $sql) ?? $sql;
        foreach (array_filter(array_map('trim', explode(';', $sql))) as $statement) {
            if ($statement !== '') {
                db()->exec($statement);
            }
        }
    }

    $ready = true;
}

function content_load(): array
{
    $defaults = content_defaults();

    try {
        content_ensure_tables();

        if (!content_has_data()) {
            $seed = content_defaults_from_legacy_json($defaults);
            content_save($seed);
            return $seed;
        }

        return content_fetch_from_tables($defaults);
    } catch (Throwable $e) {
        return $defaults;
    }
}

function content_has_data(): bool
{
    $count = (int) db()->query('SELECT COUNT(*) FROM site_settings')->fetchColumn();
    return $count > 0;
}

function content_defaults_from_legacy_json(array $defaults): array
{
    try {
        $stmt = db()->query('SELECT payload FROM site_content WHERE id = 1 LIMIT 1');
        $row = $stmt->fetch();
        if ($row && !empty($row['payload'])) {
            $decoded = json_decode((string) $row['payload'], true);
            if (is_array($decoded)) {
                return content_merge($defaults, $decoded);
            }
        }
    } catch (Throwable $e) {
        // table absente : ignorer
    }

    return $defaults;
}

function content_fetch_from_tables(array $defaults): array
{
    $settingsRow = db()->query('SELECT * FROM site_settings WHERE id = 1 LIMIT 1')->fetch() ?: [];

    $phones = db()->query('SELECT label, number, tel FROM site_phones ORDER BY sort_order ASC, id ASC')->fetchAll() ?: [];
    $whatsapp = db()->query('SELECT label, url FROM site_whatsapp ORDER BY sort_order ASC, id ASC')->fetchAll() ?: [];

    $hero = db()->query('SELECT eyebrow, title, `lead`, cta_primary, cta_secondary, image, image_alt FROM site_hero WHERE id = 1 LIMIT 1')->fetch() ?: [];

    $services = db()->query('SELECT title, description, icon FROM site_services ORDER BY sort_order ASC, id ASC')->fetchAll() ?: [];
    $projects = db()->query('SELECT title, description, category, year FROM site_projects ORDER BY sort_order ASC, id ASC')->fetchAll() ?: [];

    $aboutRow = db()->query('SELECT `lead`, mission FROM site_about WHERE id = 1 LIMIT 1')->fetch() ?: [];
    $aboutValues = db()->query('SELECT title, text FROM site_about_values ORDER BY sort_order ASC, id ASC')->fetchAll() ?: [];

    $whyRow = db()->query('SELECT title, `lead` FROM site_why WHERE id = 1 LIMIT 1')->fetch() ?: [];
    $bullets = db()->query('SELECT text FROM site_why_bullets ORDER BY sort_order ASC, id ASC')->fetchAll() ?: [];
    $features = db()->query('SELECT title, description FROM site_why_features ORDER BY sort_order ASC, id ASC')->fetchAll() ?: [];

    $testimonials = db()->query('SELECT text, name, role FROM site_testimonials ORDER BY sort_order ASC, id ASC')->fetchAll() ?: [];

    $content = [
        'settings' => [
            'site_name' => (string) ($settingsRow['site_name'] ?? $defaults['settings']['site_name']),
            'tagline' => (string) ($settingsRow['tagline'] ?? $defaults['settings']['tagline']),
            'email' => (string) ($settingsRow['email'] ?? $defaults['settings']['email']),
            'address' => (string) ($settingsRow['address'] ?? $defaults['settings']['address']),
            'meta_description' => (string) ($settingsRow['meta_description'] ?? $defaults['settings']['meta_description']),
            'phones' => $phones !== [] ? $phones : $defaults['settings']['phones'],
            'whatsapp' => $whatsapp !== [] ? $whatsapp : $defaults['settings']['whatsapp'],
        ],
        'hero' => $hero !== [] ? $hero : $defaults['hero'],
        'services' => $services !== [] ? $services : $defaults['services'],
        'projects' => $projects !== [] ? $projects : $defaults['projects'],
        'about' => [
            'lead' => (string) ($aboutRow['lead'] ?? $defaults['about']['lead']),
            'mission' => (string) ($aboutRow['mission'] ?? $defaults['about']['mission']),
            'values' => $aboutValues !== [] ? $aboutValues : $defaults['about']['values'],
        ],
        'why' => [
            'title' => (string) ($whyRow['title'] ?? $defaults['why']['title']),
            'lead' => (string) ($whyRow['lead'] ?? $defaults['why']['lead']),
            'bullets' => $bullets !== [] ? array_column($bullets, 'text') : $defaults['why']['bullets'],
            'features' => $features !== [] ? $features : $defaults['why']['features'],
        ],
        'testimonials' => $testimonials !== [] ? $testimonials : $defaults['testimonials'],
    ];

    return content_merge($defaults, $content);
}

function content_save(array $content): void
{
    content_ensure_tables();
    $pdo = db();
    $pdo->beginTransaction();

    try {
        $settings = $content['settings'] ?? [];
        $stmt = $pdo->prepare(
            'INSERT INTO site_settings (id, site_name, tagline, email, address, meta_description)
             VALUES (1, :site_name, :tagline, :email, :address, :meta_description)
             ON DUPLICATE KEY UPDATE
               site_name = VALUES(site_name),
               tagline = VALUES(tagline),
               email = VALUES(email),
               address = VALUES(address),
               meta_description = VALUES(meta_description)'
        );
        $stmt->execute([
            'site_name' => (string) ($settings['site_name'] ?? 'SowCoder'),
            'tagline' => (string) ($settings['tagline'] ?? ''),
            'email' => (string) ($settings['email'] ?? ''),
            'address' => (string) ($settings['address'] ?? ''),
            'meta_description' => (string) ($settings['meta_description'] ?? ''),
        ]);

        $pdo->exec('DELETE FROM site_phones');
        $phoneStmt = $pdo->prepare(
            'INSERT INTO site_phones (label, number, tel, sort_order) VALUES (:label, :number, :tel, :sort_order)'
        );
        foreach (array_values($settings['phones'] ?? []) as $i => $phone) {
            if (!is_array($phone)) {
                continue;
            }
            $number = trim((string) ($phone['number'] ?? ''));
            if ($number === '') {
                continue;
            }
            $tel = trim((string) ($phone['tel'] ?? ''));
            if ($tel === '') {
                $tel = preg_replace('/\s+/', '', $number) ?? $number;
            }
            $phoneStmt->execute([
                'label' => trim((string) ($phone['label'] ?? '')),
                'number' => $number,
                'tel' => $tel,
                'sort_order' => $i,
            ]);
        }

        $pdo->exec('DELETE FROM site_whatsapp');
        $waStmt = $pdo->prepare(
            'INSERT INTO site_whatsapp (label, url, sort_order) VALUES (:label, :url, :sort_order)'
        );
        foreach (array_values($settings['whatsapp'] ?? []) as $i => $wa) {
            if (!is_array($wa)) {
                continue;
            }
            $url = trim((string) ($wa['url'] ?? ''));
            if ($url === '') {
                continue;
            }
            $waStmt->execute([
                'label' => trim((string) ($wa['label'] ?? '')),
                'url' => $url,
                'sort_order' => $i,
            ]);
        }

        $hero = $content['hero'] ?? [];
        $heroStmt = $pdo->prepare(
            'INSERT INTO site_hero (id, eyebrow, title, `lead`, cta_primary, cta_secondary, image, image_alt)
             VALUES (1, :eyebrow, :title, :lead, :cta_primary, :cta_secondary, :image, :image_alt)
             ON DUPLICATE KEY UPDATE
               eyebrow = VALUES(eyebrow),
               title = VALUES(title),
               `lead` = VALUES(`lead`),
               cta_primary = VALUES(cta_primary),
               cta_secondary = VALUES(cta_secondary),
               image = VALUES(image),
               image_alt = VALUES(image_alt)'
        );
        $heroStmt->execute([
            'eyebrow' => (string) ($hero['eyebrow'] ?? ''),
            'title' => (string) ($hero['title'] ?? ''),
            'lead' => (string) ($hero['lead'] ?? ''),
            'cta_primary' => (string) ($hero['cta_primary'] ?? ''),
            'cta_secondary' => (string) ($hero['cta_secondary'] ?? ''),
            'image' => (string) ($hero['image'] ?? ''),
            'image_alt' => (string) ($hero['image_alt'] ?? ''),
        ]);

        $pdo->exec('DELETE FROM site_services');
        $serviceStmt = $pdo->prepare(
            'INSERT INTO site_services (title, description, icon, sort_order) VALUES (:title, :description, :icon, :sort_order)'
        );
        foreach (array_values($content['services'] ?? []) as $i => $row) {
            if (!is_array($row) || trim((string) ($row['title'] ?? '')) === '') {
                continue;
            }
            $serviceStmt->execute([
                'title' => trim((string) $row['title']),
                'description' => trim((string) ($row['description'] ?? '')),
                'icon' => trim((string) ($row['icon'] ?? 'code')),
                'sort_order' => $i,
            ]);
        }

        $pdo->exec('DELETE FROM site_projects');
        $projectStmt = $pdo->prepare(
            'INSERT INTO site_projects (title, description, category, year, sort_order)
             VALUES (:title, :description, :category, :year, :sort_order)'
        );
        foreach (array_values($content['projects'] ?? []) as $i => $row) {
            if (!is_array($row) || trim((string) ($row['title'] ?? '')) === '') {
                continue;
            }
            $projectStmt->execute([
                'title' => trim((string) $row['title']),
                'description' => trim((string) ($row['description'] ?? '')),
                'category' => trim((string) ($row['category'] ?? '')),
                'year' => trim((string) ($row['year'] ?? '')),
                'sort_order' => $i,
            ]);
        }

        $about = $content['about'] ?? [];
        $aboutStmt = $pdo->prepare(
            'INSERT INTO site_about (id, `lead`, mission) VALUES (1, :lead, :mission)
             ON DUPLICATE KEY UPDATE `lead` = VALUES(`lead`), mission = VALUES(mission)'
        );
        $aboutStmt->execute([
            'lead' => (string) ($about['lead'] ?? ''),
            'mission' => (string) ($about['mission'] ?? ''),
        ]);

        $pdo->exec('DELETE FROM site_about_values');
        $valueStmt = $pdo->prepare(
            'INSERT INTO site_about_values (title, text, sort_order) VALUES (:title, :text, :sort_order)'
        );
        foreach (array_values($about['values'] ?? []) as $i => $row) {
            if (!is_array($row) || trim((string) ($row['title'] ?? '')) === '') {
                continue;
            }
            $valueStmt->execute([
                'title' => trim((string) $row['title']),
                'text' => trim((string) ($row['text'] ?? '')),
                'sort_order' => $i,
            ]);
        }

        $why = $content['why'] ?? [];
        $whyStmt = $pdo->prepare(
            'INSERT INTO site_why (id, title, `lead`) VALUES (1, :title, :lead)
             ON DUPLICATE KEY UPDATE title = VALUES(title), `lead` = VALUES(`lead`)'
        );
        $whyStmt->execute([
            'title' => (string) ($why['title'] ?? ''),
            'lead' => (string) ($why['lead'] ?? ''),
        ]);

        $pdo->exec('DELETE FROM site_why_bullets');
        $bulletStmt = $pdo->prepare(
            'INSERT INTO site_why_bullets (text, sort_order) VALUES (:text, :sort_order)'
        );
        foreach (array_values($why['bullets'] ?? []) as $i => $text) {
            $text = trim((string) $text);
            if ($text === '') {
                continue;
            }
            $bulletStmt->execute(['text' => $text, 'sort_order' => $i]);
        }

        $pdo->exec('DELETE FROM site_why_features');
        $featureStmt = $pdo->prepare(
            'INSERT INTO site_why_features (title, description, sort_order) VALUES (:title, :description, :sort_order)'
        );
        foreach (array_values($why['features'] ?? []) as $i => $row) {
            if (!is_array($row) || trim((string) ($row['title'] ?? '')) === '') {
                continue;
            }
            $featureStmt->execute([
                'title' => trim((string) $row['title']),
                'description' => trim((string) ($row['description'] ?? '')),
                'sort_order' => $i,
            ]);
        }

        $pdo->exec('DELETE FROM site_testimonials');
        $testimonialStmt = $pdo->prepare(
            'INSERT INTO site_testimonials (text, name, role, sort_order) VALUES (:text, :name, :role, :sort_order)'
        );
        foreach (array_values($content['testimonials'] ?? []) as $i => $row) {
            if (!is_array($row) || trim((string) ($row['text'] ?? '')) === '') {
                continue;
            }
            $testimonialStmt->execute([
                'text' => trim((string) $row['text']),
                'name' => trim((string) ($row['name'] ?? '')),
                'role' => trim((string) ($row['role'] ?? '')),
                'sort_order' => $i,
            ]);
        }

        $pdo->commit();
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $e;
    }
}

function content_merge(array $defaults, array $stored): array
{
    foreach ($defaults as $key => $value) {
        if (!array_key_exists($key, $stored)) {
            $stored[$key] = $value;
            continue;
        }

        if (is_array($value) && content_is_assoc($value) && is_array($stored[$key])) {
            $stored[$key] = content_merge($value, $stored[$key]);
        }
    }

    return $stored;
}

function content_is_assoc(array $array): bool
{
    if ($array === []) {
        return false;
    }

    return array_keys($array) !== range(0, count($array) - 1);
}

function apply_content_settings(array &$config, array $content): void
{
    $settings = $content['settings'] ?? [];
    if (!is_array($settings)) {
        return;
    }

    foreach (['site_name', 'tagline', 'email', 'address', 'meta_description'] as $key) {
        if (isset($settings[$key]) && is_string($settings[$key]) && $settings[$key] !== '') {
            $config[$key] = $settings[$key];
        }
    }

    if (!empty($settings['phones']) && is_array($settings['phones'])) {
        $config['phones'] = array_values(array_filter($settings['phones'], static function ($phone) {
            return is_array($phone) && trim((string) ($phone['number'] ?? '')) !== '';
        }));
    }

    if (!empty($settings['whatsapp']) && is_array($settings['whatsapp'])) {
        $config['whatsapp'] = array_values(array_filter($settings['whatsapp'], static function ($wa) {
            return is_array($wa) && trim((string) ($wa['url'] ?? '')) !== '';
        }));
    }
}

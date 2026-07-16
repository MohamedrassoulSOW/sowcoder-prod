<?php

declare(strict_types=1);

auth_require_admin();

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    redirect_to('admin');
}

if (!csrf_verify($_POST['_csrf'] ?? null)) {
    redirect_to('admin', ['tab' => (string) ($_POST['tab'] ?? 'overview'), 'status' => 'csrf']);
}

$tab = (string) ($_POST['tab'] ?? 'overview');
$content = content_load();
$action = (string) ($_POST['action'] ?? 'save');

try {
    if ($action === 'delete_message') {
        $id = (int) ($_POST['message_id'] ?? 0);
        if ($id > 0) {
            $stmt = db()->prepare('DELETE FROM contact_messages WHERE id = :id');
            $stmt->execute(['id' => $id]);
        }
        redirect_to('admin', ['tab' => 'messages', 'status' => 'deleted']);
    }

    if ($tab === 'settings') {
        $phones = [];
        foreach ((array) ($_POST['phones'] ?? []) as $phone) {
            if (!is_array($phone)) {
                continue;
            }
            $number = trim((string) ($phone['number'] ?? ''));
            if ($number === '') {
                continue;
            }
            $tel = preg_replace('/\s+/', '', $number) ?? $number;
            $phones[] = [
                'label' => trim((string) ($phone['label'] ?? '')),
                'number' => $number,
                'tel' => $tel,
            ];
        }

        $whatsapp = [];
        foreach ((array) ($_POST['whatsapp'] ?? []) as $wa) {
            if (!is_array($wa)) {
                continue;
            }
            $url = trim((string) ($wa['url'] ?? ''));
            if ($url === '') {
                continue;
            }
            $whatsapp[] = [
                'label' => trim((string) ($wa['label'] ?? '')),
                'url' => $url,
            ];
        }

        $content['settings'] = [
            'site_name' => trim((string) ($_POST['site_name'] ?? 'SowCoder')),
            'tagline' => trim((string) ($_POST['tagline'] ?? '')),
            'email' => trim((string) ($_POST['email'] ?? '')),
            'address' => trim((string) ($_POST['address'] ?? '')),
            'meta_description' => trim((string) ($_POST['meta_description'] ?? '')),
            'phones' => $phones,
            'whatsapp' => $whatsapp,
        ];
    }

    if ($tab === 'hero') {
        $content['hero'] = [
            'eyebrow' => trim((string) ($_POST['eyebrow'] ?? '')),
            'title' => trim((string) ($_POST['title'] ?? '')),
            'lead' => trim((string) ($_POST['lead'] ?? '')),
            'cta_primary' => trim((string) ($_POST['cta_primary'] ?? '')),
            'cta_secondary' => trim((string) ($_POST['cta_secondary'] ?? '')),
            'image' => trim((string) ($_POST['image'] ?? '')),
            'image_alt' => trim((string) ($_POST['image_alt'] ?? '')),
        ];
    }

    if ($tab === 'services') {
        $services = [];
        foreach ((array) ($_POST['services'] ?? []) as $row) {
            if (!is_array($row)) {
                continue;
            }
            $title = trim((string) ($row['title'] ?? ''));
            $description = trim((string) ($row['description'] ?? ''));
            if ($title === '' && $description === '') {
                continue;
            }
            $services[] = [
                'title' => $title,
                'description' => $description,
                'icon' => trim((string) ($row['icon'] ?? 'code')),
            ];
        }
        $content['services'] = $services;
    }

    if ($tab === 'projects') {
        $projects = [];
        foreach ((array) ($_POST['projects'] ?? []) as $row) {
            if (!is_array($row)) {
                continue;
            }
            $title = trim((string) ($row['title'] ?? ''));
            if ($title === '') {
                continue;
            }
            $projects[] = [
                'title' => $title,
                'description' => trim((string) ($row['description'] ?? '')),
                'category' => trim((string) ($row['category'] ?? '')),
                'year' => trim((string) ($row['year'] ?? '')),
            ];
        }
        $content['projects'] = $projects;
    }

    if ($tab === 'about') {
        $values = [];
        foreach ((array) ($_POST['values'] ?? []) as $row) {
            if (!is_array($row)) {
                continue;
            }
            $title = trim((string) ($row['title'] ?? ''));
            $text = trim((string) ($row['text'] ?? ''));
            if ($title === '' && $text === '') {
                continue;
            }
            $values[] = ['title' => $title, 'text' => $text];
        }
        $content['about'] = [
            'lead' => trim((string) ($_POST['lead'] ?? '')),
            'mission' => trim((string) ($_POST['mission'] ?? '')),
            'values' => $values,
        ];
    }

    if ($tab === 'why') {
        $bullets = preg_split('/\r\n|\r|\n/', (string) ($_POST['bullets'] ?? '')) ?: [];
        $bullets = array_values(array_filter(array_map('trim', $bullets), static fn ($b) => $b !== ''));

        $features = [];
        foreach ((array) ($_POST['features'] ?? []) as $row) {
            if (!is_array($row)) {
                continue;
            }
            $title = trim((string) ($row['title'] ?? ''));
            $description = trim((string) ($row['description'] ?? ''));
            if ($title === '' && $description === '') {
                continue;
            }
            $features[] = ['title' => $title, 'description' => $description];
        }

        $content['why'] = [
            'title' => trim((string) ($_POST['title'] ?? '')),
            'lead' => trim((string) ($_POST['lead'] ?? '')),
            'bullets' => $bullets,
            'features' => $features,
        ];
    }

    if ($tab === 'testimonials') {
        $items = [];
        foreach ((array) ($_POST['testimonials'] ?? []) as $row) {
            if (!is_array($row)) {
                continue;
            }
            $text = trim((string) ($row['text'] ?? ''));
            if ($text === '') {
                continue;
            }
            $items[] = [
                'text' => $text,
                'name' => trim((string) ($row['name'] ?? '')),
                'role' => trim((string) ($row['role'] ?? '')),
            ];
        }
        $content['testimonials'] = $items;
    }

    content_save($content);
    redirect_to('admin', ['tab' => $tab, 'status' => 'saved']);
} catch (Throwable $e) {
    redirect_to('admin', ['tab' => $tab, 'status' => 'error']);
}

<?php

require '../vendor/autoload.php'; // Include Composer's autoloader

use Google\Cloud\Firestore\FirestoreClient;
use Kreait\Firebase\Factory;

// Path ke file Service Account JSON yang telah Anda download
$serviceAccountPath = __DIR__ . '/../bbm-monitoring-d4d9c0dd382e.json';

// Inisialisasi Firebase
$factory = (new Factory)->withServiceAccount($serviceAccountPath);

// Membuat instance Firestore
$firestore = $factory->createFirestore();
$database = $firestore->database();

// Data yang ingin diunggah
$data = [
    'nama' => 'Ryan Purnomo',
    'umur' => 21,
    'pekerjaan' => 'DevOps Engineer'
];

// Menentukan koleksi dan dokumen
$collectionReference = $database->collection('users');
$documentReference = $collectionReference->document('user_002');

// Menambahkan data ke Firestore
$documentReference->set($data);

echo "Data berhasil diunggah ke Firestore!";

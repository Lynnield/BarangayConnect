<?php

$headers = [
    'full_name', 'gender', 'birthdate', 'civil_status',
    'address', 'contact_number', 'email', 'occupation',
];

$first = [
    'Maria', 'Juan', 'Ana', 'Jose', 'Rosa', 'Pedro', 'Carmen', 'Antonio', 'Elena', 'Miguel',
    'Liza', 'Ramon', 'Grace', 'Carlo', 'Joy', 'Mark', 'Anna', 'Paul', 'Jenny', 'Ryan',
    'Faith', 'Noel', 'Hope', 'Leo', 'Maya', 'Ian', 'Bea', 'Kyle', 'Rica', 'Dan',
    'Angelica', 'Francis', 'Christine', 'Albert', 'Patricia', 'Roberto', 'Michelle', 'Eduardo',
];

$last = [
    'Santos', 'Reyes', 'Cruz', 'Bautista', 'Garcia', 'Mendoza', 'Torres', 'Flores', 'Rivera', 'Ramos',
    'Aquino', 'Castillo', 'Dela Cruz', 'Villanueva', 'Fernandez', 'Gonzales', 'Lopez', 'Martinez', 'Pascual', 'Soriano',
    'Domingo', 'Navarro', 'Salazar', 'Valdez', 'Morales', 'Jimenez', 'Perez', 'Evangelista', 'Santiago', 'Tolentino',
];

$streets = ['Rizal St.', 'Mabini Ave.', 'Bonifacio Rd.', 'Luna St.', 'Quezon Ave.', 'Aguinaldo St.', 'Del Pilar St.', 'Burgos St.'];
$puroks = ['Purok 1', 'Purok 2', 'Purok 3', 'Purok 4', 'Purok Maligaya', 'Purok Masagana', 'Purok Pag-asa'];
$civil = ['single', 'married', 'widowed', 'separated', 'divorced'];
$jobs = [
    'Teacher', 'Farmer', 'Driver', 'Nurse', 'Engineer', 'Vendor', 'Student', 'Clerk',
    'Carpenter', 'Electrician', 'Barangay Staff', 'Housewife', 'OFW', 'Security Guard',
    'Tailor', 'Mechanic', 'Accountant', 'Chef', 'Sales Associate', 'Fisherman',
];

$path = __DIR__ . '/residents_sample_100.csv';
$out = fopen($path, 'w');
fputcsv($out, $headers);

for ($i = 1; $i <= 100; $i++) {
    $fn = $first[array_rand($first)];
    $ln = $last[array_rand($last)];
    $full = $fn . ' ' . chr(65 + rand(0, 25)) . '. ' . $ln;

    $gender = ($i % 17 === 0) ? 'other' : (rand(0, 1) ? 'male' : 'female');
    $birth = sprintf('%04d-%02d-%02d', rand(1958, 2004), rand(1, 12), rand(1, 28));
    $status = $civil[array_rand($civil)];
    $address = rand(1, 999) . ' ' . $streets[array_rand($streets)]
        . ', ' . $puroks[array_rand($puroks)]
        . ', Brgy. San Jose, Cagayan de Oro City';
    $phone = '09' . rand(10, 99) . rand(1000000, 9999999);
    $email = strtolower(preg_replace('/\s+/', '.', $fn)) . '.' . strtolower(str_replace(' ', '', $ln)) . $i . '@sample.mail';
    $occupation = $jobs[array_rand($jobs)];

    fputcsv($out, [$full, $gender, $birth, $status, $address, $phone, $email, $occupation]);
}

fclose($out);
echo "Written: {$path}\n";

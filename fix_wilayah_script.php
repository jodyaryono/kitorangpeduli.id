<?php
// Check and fix wilayah data
echo "=== Checking Province Data ===\n";
$provinces = DB::table('provinces')->whereIn('code', ['34', '92'])->orderBy('code')->get(['id', 'code', 'name']);
foreach ($provinces as $prov) {
    echo sprintf("ID: %s, Code: %s, Name: %s\n", $prov->id, $prov->code, $prov->name);
}

echo "\n=== Checking Regencies with code 34xx ===\n";
$regencies34 = DB::table('regencies')->where('code', 'like', '34%')->orderBy('code')->get(['id', 'code', 'name', 'province_id']);
foreach ($regencies34 as $reg) {
    echo sprintf("ID: %s, Code: %s, Name: %s, Province_ID: %s\n", $reg->id, $reg->code, $reg->name, $reg->province_id);
}

echo "\n=== Checking Regencies with code 92xx ===\n";
$regencies92 = DB::table('regencies')->where('code', 'like', '92%')->orderBy('code')->get(['id', 'code', 'name', 'province_id']);
foreach ($regencies92 as $reg) {
    echo sprintf("ID: %s, Code: %s, Name: %s, Province_ID: %s\n", $reg->id, $reg->code, $reg->name, $reg->province_id);
}

echo "\n=== Fix Data ===\n";
// Get correct province IDs
$yogyakarta = DB::table('provinces')->where('code', '34')->first();
$papua_barat = DB::table('provinces')->where('code', '92')->first();

if ($yogyakarta && $papua_barat) {
    echo sprintf("DI Yogyakarta: ID %s\n", $yogyakarta->id);
    echo sprintf("Papua Barat: ID %s\n", $papua_barat->id);

    // Fix regencies with code 34xx to Yogyakarta
    $updated34 = DB::table('regencies')
        ->where('code', 'like', '34%')
        ->update(['province_id' => $yogyakarta->id]);
    echo sprintf("Updated %d regencies (34xx) to Yogyakarta (province_id: %s)\n", $updated34, $yogyakarta->id);

    // Fix regencies with code 92xx to Papua Barat
    $updated92 = DB::table('regencies')
        ->where('code', 'like', '92%')
        ->update(['province_id' => $papua_barat->id]);
    echo sprintf("Updated %d regencies (92xx) to Papua Barat (province_id: %s)\n", $updated92, $papua_barat->id);
} else {
    echo "ERROR: Province not found!\n";
}

echo "\n=== Verification ===\n";
echo "Yogyakarta regencies:\n";
$check34 = DB::table('regencies')->where('province_id', $yogyakarta->id)->count();
echo sprintf("Total: %d\n", $check34);

echo "\nPapua Barat regencies:\n";
$check92 = DB::table('regencies')->where('province_id', $papua_barat->id)->count();
echo sprintf("Total: %d\n", $check92);

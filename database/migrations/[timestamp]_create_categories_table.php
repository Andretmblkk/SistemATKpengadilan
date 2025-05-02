Schema::create('categories', function (Blueprint $table) {
    $table->id();
    $table->string('name'); // kolom name wajib diisi
    $table->text('description')->nullable(); // kolom description boleh kosong
    $table->timestamps();
});
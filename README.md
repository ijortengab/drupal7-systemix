Systemix
========

Kumpulan library independent siap pakai.

- Auto UPPERCASE when typing.

- Limited input for number only.

## Library Form Conditional dengan Symfony/EventDispatcher

Contoh, Terdapat Node Form dengan conditional field sbb:

Field Jenis Kelamin:
    - laki-laki
    - perempuan
Field Apakah sudah sunat:
    - Iya
    - Belum
Field Apakah sudah menikah:
    - Belum Pernah
    - Sudah menikah
    - Pernah menikah

Penjelasan :
- User dihadapkan pada form dengan hanya tersedia satu pilihan yakni
  `Field Jenis Kelamin`. Pilihan lainnya hide.
- Pada `Field Jenis Kelamin`, jika User memilih `laki-laki`, maka munculkan
  `Field Apakah sudah sunat`.
- Pada `Field Apakah sudah sunat`, apapun pilihan User, maka munculkan
  `Field Apakah sudah menikah`.
- Jika pada `Field Jenis Kelamin`, jika User memilih `perempuan`, maka langsung
  loncat menampilkan `Field Apakah sudah menikah`.
- `Field Apakah sudah sunat` hanya muncul jika User pada `Field Jenis Kelamin`
  memilih `laki-laki`.

Solusi dengan cara if else, bisa dilakukan dengan code sbb:

```
$is_perempuan = ($form->jenis_kelamin->getValue() == 'perempuan');
$is_laki_laki = ($form->jenis_kelamin->getValue() == 'laki-laki');
$sunat_filled = ($form->apakah_sunat->hasValue());

if ($is_laki_laki) {
    $form->apakah_sunat->show();
}
else {
    $form->apakah_sunat->hide();
}
if ($is_perempuan || ($is_laki_laki && $sunat_filled)) {
    $form->status_menikah->show();
}
else {
    $form->status_menikah->hide();
}
```

Library ini menyediakan solusi lain dengan menggunakan EventDispatcher.

Contoh code adalah sebagai berikut:

```
$form->apakah_sunat->listen('jenis_kelamin','on_value_changed', function ($event) {
    if ($event->getElementChanged()->getValue() == 'laki-laki') {
        $event->getElementListener()->show();
    }
});
$form->status_menikah->listen('jenis_kelamin','on_value_changed', function ($event) {
    if ($event->getElementChanged()->getValue('machine_name') == 'retur') {
        $event->getElementListener()->show();
    }
});
$form->status_menikah->listen('apakah_sunat','on_value_changed', function ($event) {
    if ($event->getElementChanged()->hasValue()) {
        $event->getElementListener()->show();
    }
});
// Khusus untuk node edit form existing.
$form->status_menikah->listen('apakah_sunat','on_display_changed', function ($event) {
    if ($event->getElementChanged()->isVisible()) {
        $event->getElementListener()->show();
    };
});
```

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class ResidentHealthResponse extends Model
{
    protected $fillable = [
        'resident_id',
        'response_id',
        'question_code',
        'answer',
    ];

    /**
     * Get the resident that owns this health response.
     */
    public function resident(): BelongsTo
    {
        return $this->belongsTo(Resident::class);
    }

    /**
     * Get the questionnaire response that owns this health response.
     */
    public function response(): BelongsTo
    {
        return $this->belongsTo(Response::class);
    }

    /**
     * Question codes and their descriptions
     */
    public static function getQuestionDescriptions(): array
    {
        return [
            'jkn' => '1. Apakah mempunyai kartu jaminan kesehatan atau JKN?',
            'merokok' => '2. Apakah Saudara merokok?',
            'jamban' => '3. Apakah Saudara biasa buang air besar di jamban?',
            'air_bersih' => '4. Apakah Saudara biasa menggunakan air bersih?',
            'tb_paru' => '5. Apakah Saudara pernah didiagnosis menderita TB paru?',
            'obat_tbc' => '6. Bila ya, apakah meminum obat TBC secara teratur?',
            'gejala_tb' => '7. Apakah Saudara pernah menderita batuk berdahak > 2 minggu dengan gejala?',
            'hipertensi' => '8. Apakah Saudara pernah didiagnosis menderita hipertensi?',
            'obat_hipertensi' => '9. Bila ya, apakah meminum obat hipertensi secara teratur?',
            'ukur_tensi' => '10a. Apakah dilakukan pengukuran tekanan darah?',
            'sistolik' => '10b.1. Hasil pengukuran tekanan darah - Sistolik (mmHg)',
            'diastolik' => '10b.2. Hasil pengukuran tekanan darah - Diastolik (mmHg)',
            'kontrasepsi' => '11. Apakah Saudara menggunakan alat/obat/cara kontrasepsi?',
            'melahirkan_faskes' => '12. Apakah Anak Saudara lahir di fasilitas kesehatan?',
            'asi_eksklusif' => '13. Apakah anak ini diberi ASI saja sejak lahir hingga berumur 6 bulan (ASI Eksklusif)?',
            'imunisasi_lengkap' => '14. Apakah anak ini mempunyai buku KIA atau Kartu/Buku Catatan Imunisasi?',
            'pemantauan_balita' => '15. Apakah anak ini memiliki KMS (Kartu Menuju Sehat)?',
        ];
    }

    /**
     * Get answer label for radio buttons (1=Ya, 2=Tidak)
     */
    public function getAnswerLabelAttribute(): string
    {
        if (in_array($this->question_code, ['sistolik', 'diastolik'])) {
            return $this->answer . ' mmHg';
        }

        return match ($this->answer) {
            '1' => 'Ya',
            '2' => 'Tidak',
            default => $this->answer ?? '-'
        };
    }
}

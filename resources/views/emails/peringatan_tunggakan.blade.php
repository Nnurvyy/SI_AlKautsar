Assalamu'alaikum Warahmatullahi Wabarakatuh, Sdr/i {{ $data['nama_jamaah'] }}.

Semoga Anda dalam keadaan sehat walafiat.

Kami menginformasikan status Tabungan Qurban Anda:
--------------------------------------------------
No. Tabungan    : {{ substr($data['no_tabungan'], 0, 8) }}
Rincian Hewan   : {{ $data['list_hewan'] }}
Target Total    : Rp {{ number_format($data['target_total'], 0, ',', '.') }}
Target s/d Bln {{ $data['bulan_ke'] }} : Rp {{ number_format($data['target_akumulasi'], 0, ',', '.') }}
--------------------------------------------------

Total Setoran Masuk (Verified): Rp {{ number_format($data['total_terkumpul'], 0, ',', '.') }}.

Saat ini terdapat selisih/tunggakan target sebesar: **Rp {{ number_format($data['sisa_kekurangan'], 0, ',', '.') }}**.

Mohon kesediaannya untuk melakukan setoran agar target Qurban tercapai tepat waktu.
Abaikan pesan ini jika Anda baru saja melakukan pembayaran (sedang proses verifikasi).

Wassalamu'alaikum,
Admin Masjid
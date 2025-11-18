<?php



namespace App\Models;



use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;



class BanAn extends Model

{

    use HasFactory;



    // Tên bảng trong database

    protected $table = 'ban_an';



    // Các cột có thể được gán giá trị hàng loạt (mass assignable)

    protected $fillable = [

        'khu_vuc_id',

        'so_ban',

        'ma_qr',

        'duong_dan_qr',

        'so_ghe',

        'trang_thai'

    ];





    public $timestamps = true;





    public function khuVuc()

    {



        return $this->belongsTo(KhuVuc::class, 'khu_vuc_id');

    }

}
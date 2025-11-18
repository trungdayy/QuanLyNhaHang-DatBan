namespace App\Http\Controllers\Shop\Oderqr; 

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// Models (Đã sửa lỗi cú pháp)
use App\Models\DatBan;
use App\Models\DanhMuc; 
use App\Models\MonTrongCombo;
use App\Models\OrderMon;
use App\Models\ChiTietOrder;
use App\Models\MonAn; 
use App\Models\ComboBuffet; 
use App\Models\BanAn; 

// Thư viện
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Route; 
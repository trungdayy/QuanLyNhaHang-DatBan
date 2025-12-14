<?php

namespace App\Events;

use App\Models\ChiTietOrder;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MonAnHoanThanh implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $chiTietOrder;
    public $nhanVienId;

    /**
     * Create a new event instance.
     *
     * @param ChiTietOrder $chiTietOrder Đối tượng món ăn vừa nấu xong
     * @param int $nhanVienId ID nhân viên phụ trách bàn đó
     */
    public function __construct(ChiTietOrder $chiTietOrder, $nhanVienId)
    {
        $this->chiTietOrder = $chiTietOrder;
        $this->nhanVienId = $nhanVienId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        // Tạo kênh riêng tư: notify.nhanvien.{id_nhan_vien}
        // Chỉ nhân viên có ID này mới nghe được
        return [
            new PrivateChannel('notify.nhanvien.' . $this->nhanVienId),
        ];
    }

    /**
     * Tên sự kiện để lắng nghe ở phía Client (Javascript)
     */
    public function broadcastAs(): string
    {
        return 'food.ready';
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class OperationReaction extends Model
{
        use  LogsActivity;

    use HasFactory;

    // ده "التصريح" اللي لارافيل طالبه عشان يقبل يحفظ الداتا دي
    protected $fillable = ['operation_id', 'user_id', 'emoji'];

    // علاقة الريأكت بالموظف اللي عمله
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // علاقة الريأكت بالرسالة
    public function operation()
    {
        return $this->belongsTo(Operation::class);
    }
}
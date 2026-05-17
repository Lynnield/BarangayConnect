<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Backup extends Model {
    protected $fillable = ['backup_name','backup_type','file_path','file_size','status','notes','generated_by','completed_at'];
    protected $casts = ['completed_at'=>'datetime'];
    public function generatedBy() { return $this->belongsTo(User::class,'generated_by'); }
    public function getFileSizeFormattedAttribute(): string {
        if (!$this->file_size) return 'N/A';
        $units=['B','KB','MB','GB']; $size=$this->file_size; $unit=0;
        while($size>=1024&&$unit<3){$size/=1024;$unit++;}
        return round($size,2).' '.$units[$unit];
    }
}

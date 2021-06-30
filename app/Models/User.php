<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    // boot 方法会在用户模型类完成初始化之后进行加载，因此我们对事件的监听需要放在该方法中。
    public static function boot()
    {
        parent::boot();
        
        // 我们要生成的用户激活令牌需要在用户模型创建之前生成，因此需要监听的是 creating 方法
        static::creating(function ($user) {
            $user->activation_token = Str::random(10);
        });
    }

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /*头像和侧边栏
    该方法主要做了以下几个操作：

    1.为 gravatar 方法传递的参数 size 指定了默认值 100；
    2.通过 $this->attributes['email'] 获取到用户的邮箱；
    3.使用 trim 方法剔除邮箱的前后空白内容；
    4.用 strtolower 方法将邮箱转换为小写；
    5.将小写的邮箱使用 md5 方法进行转码；
    6.将转码后的邮箱与链接、尺寸拼接成完整的 URL 并返回；*/
    public function gravatar($size = '100')
    {
        $hash = md5(strtolower(trim($this->attributes['email'])));
        return "http://www.gravatar.com/avatar/$hash?s=$size";
    }

    // 关联status模型
    public function statuses()
    {
        return $this->hasMany(Status::class);
    }

    // 将当前用户发布过的所有微博从数据库中取出，并根据创建时间来倒序排序
    public function feed()
    {
        return $this->statuses()
                    ->orderBy('created_at', 'desc');
    }

    // 关联followers表
    // 粉丝列表
    // 第二个参数关联表名，第三个参数 user_id 是定义在关联中的模型外键名，第四个参数 follower_id 则是要合并的模型外键名
    public function followers()
    {
        return $this->belongsToMany(User::class, 'followers', 'user_id', 'follower_id');
    }
    // 关注列表
    public function followings()
    {
        return $this->belongsToMany(User::class, 'followers', 'follower_id', 'user_id');
    }


    // 关注动作
    // is_array 用于判断参数是否为数组，如果已经是数组，则没有必要再使用 compact 方法
    public function follow($user_ids)
    {
        if ( ! is_array($user_ids)) {
            $user_ids = compact('user_ids');
        }
        $this->followings()->sync($user_ids, false);
    }
    // 取关动作
    public function unfollow($user_ids)
    {
        if ( ! is_array($user_ids)) {
            $user_ids = compact('user_ids');
        }
        $this->followings()->detach($user_ids);
    }

    // 是否关注了用户
    public function isFollowing($user_id)
    {
        return $this->followings->contains($user_id);
    }
}

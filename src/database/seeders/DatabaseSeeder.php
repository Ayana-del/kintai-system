<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Rest;
use App\Models\CorrectionRequest;
use App\Models\CorrectionDetail;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // 1. ユーザー作成（管理者 + スタッフ6名）
        User::create([
            'name' => '管理者 太郎',
            'kana' => 'かんりしゃ たろう',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 1,
            'email_verified_at' => now(),
        ]);

        $staffs = [
            ['name' => '秋田 朋美', 'kana' => 'あきた ともみ', 'email' => 'tomomi.a@example.com'],
            ['name' => '中西 敦夫', 'kana' => 'なかにし のりお', 'email' => 'norio.n@example.com'],
            ['name' => '西 怜奈', 'kana' => 'にし れいな', 'email' => 'reina.n@example.com'],
            ['name' => '増田 一世', 'kana' => 'ますだ いっせい', 'email' => 'issei.m@example.com'],
            ['name' => '山田 太郎', 'kana' => 'やまだ たろう', 'email' => 'taro.y@example.com'],
            ['name' => '山本 敬吉', 'kana' => 'やまもと けいきち', 'email' => 'keikichi.y@example.com'],
        ];

        $userInstances = [];
        foreach ($staffs as $staff) {
            $userInstances[] = User::create([
                'name' => $staff['name'],
                'kana' => $staff['kana'],
                'email' => $staff['email'],
                'password' => Hash::make('password'),
                'role' => 0,
                'email_verified_at' => now(),
            ]);
        }

        // 2. 2026年2月・3月の基本勤怠データ作成（各月22日間出勤）
        $months = ['2026-02', '2026-03'];
        foreach ($months as $monthStr) {
            $startOfMonth = Carbon::parse($monthStr)->startOfMonth();
            $daysInMonth = $startOfMonth->daysInMonth;

            foreach ($userInstances as $user) {
                // 1ヶ月の中からランダムに22日選ぶ
                $workDays = collect(range(1, $daysInMonth))->random(min(22, $daysInMonth));

                foreach ($workDays as $day) {
                    $date = $startOfMonth->copy()->day($day);
                    $dateString = $date->format('Y-m-d');

                    $attendance = Attendance::create([
                        'user_id' => $user->id,
                        'date' => $dateString,
                        'check_in' => '09:00:00',
                        'check_out' => '18:00:00',
                        'remarks' => '通常勤務',
                        'status' => 4, // 全て退勤済
                    ]);

                    Rest::create([
                        'attendance_id' => $attendance->id,
                        'rest_start' => '12:00:00',
                        'rest_end' => '13:00:00',
                    ]);
                }
            }
        }

        // 3. 修正申請データの作成（月跨ぎ・複数人・承認/未承認）
        // userInstances[2] = 西 怜奈, userInstances[0] = 秋田 朋美
        $correctionTargets = [
            // --- 西さん (3月分) ---
            ['user' => $userInstances[2], 'date' => '2026-03-02', 'status' => 0, 'reason' => '出勤打刻忘れ'],
            ['user' => $userInstances[2], 'date' => '2026-03-05', 'status' => 0, 'reason' => '電車遅延による時間修正'],
            ['user' => $userInstances[2], 'date' => '2026-03-10', 'status' => 1, 'reason' => '承認済みのテストデータ'],
            // --- 西さん (2月分) ---
            ['user' => $userInstances[2], 'date' => '2026-02-05', 'status' => 0, 'reason' => '2月分の未承認申請'],
            ['user' => $userInstances[2], 'date' => '2026-02-15', 'status' => 1, 'reason' => '2月分の承認済みデータ'],

            // --- 秋田さん (3月分) ---
            ['user' => $userInstances[0], 'date' => '2026-03-03', 'status' => 0, 'reason' => '退勤時間を間違えたため'],
            // --- 秋田さん (2月分) ---
            ['user' => $userInstances[0], 'date' => '2026-02-10', 'status' => 0, 'reason' => '過去の修正申請テスト'],
        ];

        foreach ($correctionTargets as $target) {
            // 指定した日付の勤怠データを取得
            $attendance = Attendance::where('user_id', $target['user']->id)
                ->whereDate('date', $target['date'])
                ->first();

            if ($attendance) {
                // 申請レコード作成
                $request = CorrectionRequest::create([
                    'attendance_id' => $attendance->id,
                    'user_id' => $target['user']->id,
                    'date' => $target['date'],
                    'reason' => $target['reason'],
                    'status' => $target['status'], // 0:承認待ち, 1:承認済み
                ]);

                // 404エラーを防ぐため、詳細データ（Detail）を必ず作成
                // 出勤時間の修正
                CorrectionDetail::create([
                    'correction_request_id' => $request->id,
                    'type' => 'check_in',
                    'modified_time' => '08:45:00',
                ]);

                // 退勤時間の修正
                CorrectionDetail::create([
                    'correction_request_id' => $request->id,
                    'type' => 'check_out',
                    'modified_time' => '19:15:00',
                ]);
            }
        }
    }
}

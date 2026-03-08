<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Schema;

class TestQuestion extends Model
{
    use HasFactory;

    /**
     * النقاط القابلة للتعبئة
     */
    protected $fillable = [
        'test_id',
        'question_text',
        'explanation',
        'explanation_image',
        'question_image',
        'type',
        'part',
        'module_number',
        'question_order',
        'score',
        'difficulty',
        'content',
        'correct_answer'
    ];

    /**
     * التحويل التلقائي لأنواع البيانات
     */
    protected $casts = [
        'test_id' => 'integer',
        'question_order' => 'integer',
        'score' => 'integer',
        'module_number' => 'integer',
        'difficulty' => 'string',
        'content' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * السمات المضافة
     */
    protected $appends = [
        'type_display',
        'part_display',
        'module_display',
        'module_info',
        'effective_module',
        'is_old_system',
        'is_new_system',
        'complete_info',
        'correct_answer_display',
        'image_url',
        'explanation_image_url'
    ];

    /**
     * تهيئة القيم الافتراضية والأحداث
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->module_number) && !empty($model->part)) {
                $model->module_number = self::convertPartToModule($model->part);
            }

            if (empty($model->question_order)) {
                $maxOrder = self::where('test_id', $model->test_id)
                    ->where('module_number', $model->module_number ?? self::convertPartToModule($model->part))
                    ->max('question_order');

                $model->question_order = $maxOrder ? $maxOrder + 1 : 1;
            }
        });

        static::saving(function ($model) {
            if (!empty($model->module_number) && empty($model->part)) {
                $model->part = self::convertModuleToPart($model->module_number);
            }
        });
    }

    /**
     * تحويل نص الجزء إلى رقم الموديول
     */
    public static function convertPartToModule($part): int
    {
        if (empty($part)) {
            return 1;
        }

        return match ($part) {
            'part1' => 1,
            'part2' => 2,
            'part3' => 3,
            'part4' => 4,
            'part5' => 5,
            default => (int) str_replace('part', '', $part) ?: 1
        };
    }

    /**
     * تحويل رقم الموديول إلى نص الجزء
     */
    public static function convertModuleToPart($moduleNumber): string
    {
        $moduleNumber = max(1, min(5, (int) $moduleNumber));
        return "part{$moduleNumber}";
    }

    /**
     * الحصول على رقم الموديول الفعال
     */
    public function getEffectiveModuleAttribute(): int
    {
        if (!empty($this->module_number)) {
            return $this->module_number;
        }

        return self::convertPartToModule($this->part);
    }

    /**
     * التحقق مما إذا كان يستخدم نظام الأجزاء القديم
     */
    public function getIsOldSystemAttribute(): bool
    {
        return !empty($this->part) && empty($this->module_number);
    }

    /**
     * التحقق مما إذا كان يستخدم نظام الموديولات الجديد
     */
    public function getIsNewSystemAttribute(): bool
    {
        return !empty($this->module_number);
    }

    /**
     * الحصول على اسم عرض الموديول
     */
    public function getModuleDisplayAttribute(): string
    {
        if (!$this->module_number && !$this->part) {
            return 'بدون موديول';
        }

        $moduleNum = $this->effective_module;

        if ($this->moduleRelation && $this->moduleRelation->name) {
            return $this->moduleRelation->name;
        }

        if ($this->test && method_exists($this->test, 'getModule')) {
            $module = $this->test->getModule($moduleNum);
            if ($module) {
                $moduleData = is_array($module) ? $module : $module->toArray();
                return $moduleData['name'] ?? "الموديول {$moduleNum}";
            }
        }

        if ($this->is_new_system) {
            return "الموديول {$moduleNum}";
        }

        return $this->part_display;
    }

    /**
     * الحصول على معلومات الموديول
     */
    public function getModuleInfoAttribute()
    {
        if (!$this->test) {
            return null;
        }

        $moduleNum = $this->effective_module;

        if (method_exists($this->test, 'getModule')) {
            return $this->test->getModule($moduleNum);
        }

        return null;
    }

    /**
     * الحصول على رابط صورة السؤال
     */
    public function getImageUrlAttribute()
    {
        return $this->getImageUrl();
    }

    /**
     * الحصول على رابط صورة الشرح
     */
    public function getExplanationImageUrlAttribute()
    {
        return $this->getExplanationImageUrl();
    }

    /**
     * الحصول على الاختبار الذي يمتلك السؤال
     */
    public function test(): BelongsTo
    {
        return $this->belongsTo(Test::class);
    }

    /**
     * العلاقة مع TestModule
     */
    public function moduleRelation(): BelongsTo
    {
        try {
            if (!Schema::hasTable('test_modules')) {
                return $this->belongsTo(TestModule::class)->whereNull('id');
            }
        } catch (\Exception $e) {
            return $this->belongsTo(TestModule::class)->whereNull('id');
        }

        if (!$this->module_number) {
            return $this->belongsTo(TestModule::class)->whereNull('id');
        }

        return $this->belongsTo(TestModule::class, 'module_number', 'order')
            ->where('test_id', $this->test_id);
    }

    /**
     * اسم بديل لـ moduleRelation
     */
    public function module(): BelongsTo
    {
        return $this->moduleRelation();
    }

    /**
     * الحصول على جميع الخيارات لهذا السؤال
     */
    public function options(): HasMany
    {
        return $this->hasMany(TestQuestionOption::class)->orderBy('option_order');
    }

    /**
     * الحصول على الخيار الصحيح
     */
    public function correctOption()
    {
        return $this->options()->where('is_correct', true)->first();
    }

    /**
     * الحصول على جميع الخيارات الصحيحة
     */
    public function correctOptions(): HasMany
    {
        return $this->hasMany(TestQuestionOption::class)->where('is_correct', true);
    }

    /**
     * الحصول على جميع إجابات الطلاب لهذا السؤال
     */
    public function studentAnswers(): HasMany
    {
        return $this->hasMany(StudentTestAnswer::class, 'test_question_id');
    }

    /**
     * اسم بديل لـ studentAnswers
     */
    public function answers(): HasMany
    {
        return $this->studentAnswers();
    }

    /**
     * التحقق مما إذا كان سؤال اختيار متعدد
     */
    public function isMcq(): bool
    {
        return in_array($this->type, ['mcq', 'mcd', 'multiple_choice']);
    }

    /**
     * التحقق مما إذا كان سؤال صح/خطأ
     */
    public function isTrueFalse(): bool
    {
        return in_array($this->type, ['tf', 'true_false']);
    }

    /**
     * التحقق مما إذا كان سؤال رقمي
     */
    public function isNumeric(): bool
    {
        return in_array($this->type, ['numeric', 'number']);
    }

    /**
     * التحقق مما إذا كان سؤال تحديد
     */
    public function isIdentification(): bool
    {
        return in_array($this->type, ['it', 'identification', 'text']);
    }

    /**
     * التحقق مما إذا كان نوع السؤال يطابق النوع المحدد
     */
    public function isType($type): bool
    {
        $typeMap = [
            'mcq' => ['mcq', 'mcd', 'multiple_choice'],
            'tf' => ['tf', 'true_false'],
            'numeric' => ['numeric', 'number'],
            'identification' => ['it', 'identification', 'text']
        ];

        if (isset($typeMap[$type])) {
            return in_array($this->type, $typeMap[$type]);
        }

        return $this->type === $type;
    }

    /**
     * الحصول على اسم عرض نوع السؤال
     */
    public function getTypeDisplayAttribute(): string
    {
        return match ($this->type) {
            'mcq', 'mcd', 'multiple_choice' => 'اختيار متعدد',
            'tf', 'true_false' => 'صح/خطأ',
            'numeric', 'number' => 'رقمي',
            'it', 'identification' => 'تحديد',
            'text' => 'نصي',
            default => ucfirst($this->type)
        };
    }

    /**
     * الحصول على اسم عرض الجزء
     */
    public function getPartDisplayAttribute(): string
    {
        return match ($this->part) {
            'part1' => 'الجزء 1',
            'part2' => 'الجزء 2',
            'part3' => 'الجزء 3',
            'part4' => 'الجزء 4',
            'part5' => 'الجزء 5',
            default => $this->part ?? 'بدون جزء'
        };
    }

    /**
     * التحقق من صحة الإجابة لهذا السؤال
     */
    public function validateAnswer($answer): array
    {
        $isCorrect = false;
        $scoreEarned = 0;
        $details = [];

        switch (true) {
            case $this->isMcq():
                if (is_numeric($answer) || is_string($answer)) {
                    $selectedOption = $this->options()->find($answer);
                    if ($selectedOption && $selectedOption->is_correct) {
                        $isCorrect = true;
                        $scoreEarned = $this->score;
                        $details['selected_option'] = $selectedOption->option_text;
                    }
                } elseif (is_array($answer)) {
                    $correctOptions = $this->correctOptions()->pluck('id')->toArray();
                    sort($answer);
                    sort($correctOptions);

                    if ($answer == $correctOptions) {
                        $isCorrect = true;
                        $scoreEarned = $this->score;
                    }

                    $details['selected_options'] = $answer;
                    $details['correct_options'] = $correctOptions;
                }
                break;

            case $this->isTrueFalse():
                $correctAnswer = strtolower(trim($this->correct_answer));
                $studentAnswer = strtolower(trim($answer));

                $correctAnswer = in_array($correctAnswer, ['true', '1', 'yes', 'صح', 'نعم']) ? 'true' : 'false';
                $studentAnswer = in_array($studentAnswer, ['true', '1', 'yes', 'صح', 'نعم']) ? 'true' : 'false';

                if ($correctAnswer === $studentAnswer) {
                    $isCorrect = true;
                    $scoreEarned = $this->score;
                }

                $details['correct'] = $correctAnswer;
                $details['student'] = $studentAnswer;
                break;

            case $this->isNumeric():
                $correctAnswer = (float) $this->correct_answer;
                $studentAnswer = (float) $answer;

                $margin = 0.01;
                if (abs($correctAnswer - $studentAnswer) <= $margin) {
                    $isCorrect = true;
                    $scoreEarned = $this->score;
                }

                $details['correct'] = $correctAnswer;
                $details['student'] = $studentAnswer;
                $details['difference'] = abs($correctAnswer - $studentAnswer);
                break;

            default:
                $correctAnswer = strtolower(trim($this->correct_answer));
                $studentAnswer = strtolower(trim($answer));

                if ($correctAnswer === $studentAnswer) {
                    $isCorrect = true;
                    $scoreEarned = $this->score;
                }

                $details['correct'] = $correctAnswer;
                $details['student'] = $studentAnswer;
                break;
        }

        return [
            'is_correct' => $isCorrect,
            'score_earned' => $scoreEarned,
            'max_score' => $this->score,
            'details' => $details,
            'feedback' => $isCorrect ? 'إجابة صحيحة!' : 'إجابة خاطئة. حاول مرة أخرى.'
        ];
    }

    /**
     * الحصول على تنسيق العرض للإجابة الصحيحة
     */
    public function getCorrectAnswerDisplayAttribute(): string
    {
        switch (true) {
            case $this->isMcq():
                $correctOptions = $this->correctOptions()->get();
                if ($correctOptions->count() > 1) {
                    return $correctOptions->pluck('option_text')->implode('، ');
                }
                return $correctOptions->first()->option_text ?? 'غير محدد';

            case $this->isTrueFalse():
                return in_array(strtolower($this->correct_answer), ['true', '1', 'yes', 'صح', 'نعم']) ? 'صح' : 'خطأ';

            case $this->isNumeric():
                return (string) $this->correct_answer;

            default:
                return $this->correct_answer ?? 'غير محدد';
        }
    }

    /**
     * الحصول على معلومات السؤال الكاملة مع جميع العلاقات
     */
    public function getCompleteInfoAttribute(): array
    {
        $info = [
            'id' => $this->id,
            'test_id' => $this->test_id,
            'question_text' => $this->question_text,
            'type' => $this->type,
            'type_display' => $this->type_display,
            'module' => [
                'number' => $this->module_number,
                'part' => $this->part,
                'display' => $this->module_display,
                'effective_module' => $this->effective_module,
                'is_old_system' => $this->is_old_system,
                'is_new_system' => $this->is_new_system,
            ],
            'order' => $this->question_order,
            'score' => $this->score,
            'difficulty' => $this->difficulty,
            'difficulty_label' => $this->getDifficultyLabel(),
            'content' => $this->content,
            'correct_answer' => $this->correct_answer,
            'correct_answer_display' => $this->correct_answer_display,
            'images' => [
                'question' => $this->question_image,
                'question_url' => $this->image_url,
                'explanation' => $this->explanation_image,
                'explanation_url' => $this->explanation_image_url,
            ],
            'explanation' => $this->explanation,
            'has_options' => $this->isMcq(),
            'has_image' => !empty($this->question_image),
            'has_explanation' => !empty($this->explanation) || !empty($this->explanation_image),
            'summary' => $this->getSummary(150),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        if ($this->isMcq()) {
            $info['options'] = $this->options->map(function ($option) {
                return [
                    'id' => $option->id,
                    'text' => $option->option_text,
                    'is_correct' => $option->is_correct,
                    'order' => $option->option_order
                ];
            })->toArray();
        }

        if ($this->relationLoaded('test')) {
            $info['test'] = [
                'id' => $this->test->id,
                'name' => $this->test->name ?? null,
            ];
        }

        if ($this->relationLoaded('studentAnswers')) {
            $info['statistics'] = $this->getStatistics();
        }

        return $info;
    }

    /**
     * نطاق للأسئلة في موديول محدد
     */
    public function scopeInModule($query, $moduleNumber)
    {
        return $query->where('module_number', $moduleNumber);
    }

    /**
     * نطاق للأسئلة في جزء محدد
     */
    public function scopeInPart($query, $part)
    {
        return $query->where('part', $part);
    }

    /**
     * نطاق للأسئلة بموديول فعال
     */
    public function scopeWithEffectiveModule($query, $moduleNumber)
    {
        return $query->where(function ($q) use ($moduleNumber) {
            $q->where('module_number', $moduleNumber)
                ->orWhere('part', self::convertModuleToPart($moduleNumber));
        });
    }

    /**
     * نطاق لأنواع أسئلة محددة
     */
    public function scopeOfType($query, $type)
    {
        $typeMap = [
            'mcq' => ['mcq', 'mcd', 'multiple_choice'],
            'tf' => ['tf', 'true_false'],
            'numeric' => ['numeric', 'number'],
            'identification' => ['it', 'identification', 'text']
        ];

        if (isset($typeMap[$type])) {
            return $query->whereIn('type', $typeMap[$type]);
        }

        return $query->where('type', $type);
    }

    /**
     * نطاق لأسئلة الاختيار المتعدد
     */
    public function scopeMcq($query)
    {
        return $this->scopeOfType($query, 'mcq');
    }

    /**
     * نطاق لأسئلة الصح/الخطأ
     */
    public function scopeTrueFalse($query)
    {
        return $this->scopeOfType($query, 'tf');
    }

    /**
     * نطاق للأسئلة الرقمية
     */
    public function scopeNumeric($query)
    {
        return $this->scopeOfType($query, 'numeric');
    }

    /**
     * نطاق للأسئلة النصية/التحديد
     */
    public function scopeIdentification($query)
    {
        return $this->scopeOfType($query, 'identification');
    }

    /**
     * نطاق للأسئلة ذات الصور
     */
    public function scopeWithImage($query)
    {
        return $query->whereNotNull('question_image')
            ->where('question_image', '!=', '');
    }

    /**
     * نطاق للأسئلة ذات الشرح
     */
    public function scopeWithExplanation($query)
    {
        return $query->where(function ($q) {
            $q->whereNotNull('explanation')
                ->orWhereNotNull('explanation_image');
        });
    }

    /**
     * نطاق للأسئلة حسب الصعوبة المخزنة
     */
    public function scopeByDifficulty($query, $difficulty)
    {
        return $query->where('difficulty', $difficulty);
    }

    /**
     * نطاق للأسئلة حسب المحتوى
     */
    public function scopeByContent($query, $content)
    {
        return $query->where('content', $content);
    }

    /**
     * الحصول على السؤال التالي في نفس الموديول
     */
    public function nextQuestion()
    {
        if (!$this->test) {
            return null;
        }

        return $this->test->questions()
            ->withEffectiveModule($this->effective_module)
            ->where('question_order', '>', $this->question_order)
            ->orderBy('question_order')
            ->first();
    }

    /**
     * الحصول على السؤال السابق في نفس الموديول
     */
    public function previousQuestion()
    {
        if (!$this->test) {
            return null;
        }

        return $this->test->questions()
            ->withEffectiveModule($this->effective_module)
            ->where('question_order', '<', $this->question_order)
            ->orderByDesc('question_order')
            ->first();
    }

    /**
     * الحصول على موقع السؤال في الموديول
     */
    public function getPositionInModule(): array
    {
        if (!$this->test) {
            return ['current' => 0, 'total' => 0, 'percentage' => 0];
        }

        $totalInModule = $this->test->questions()
            ->withEffectiveModule($this->effective_module)
            ->count();

        $position = $this->test->questions()
            ->withEffectiveModule($this->effective_module)
            ->where('question_order', '<=', $this->question_order)
            ->count();

        return [
            'current' => $position,
            'total' => $totalInModule,
            'percentage' => $totalInModule > 0 ? round(($position / $totalInModule) * 100) : 0,
            'remaining' => $totalInModule - $position
        ];
    }

    /**
     * الهجرة من النظام القديم إلى النظام الجديد
     */
    public function migrateToModuleSystem(): bool
    {
        if ($this->is_old_system) {
            $this->module_number = self::convertPartToModule($this->part);
            return $this->save();
        }

        return false;
    }

    /**
     * تحويل نظام الأجزاء القديم إلى رقم الموديول
     */
    public function convertPartToModuleNumber(): bool
    {
        if ($this->part && !$this->module_number) {
            $this->module_number = self::convertPartToModule($this->part);
            return $this->save();
        }

        return false;
    }

    /**
     * التحقق مما إذا كان للسؤال صورة
     */
    public function hasImage(): bool
    {
        return !empty($this->question_image);
    }

    /**
     * التحقق مما إذا كان للسؤال شرح
     */
    public function hasExplanation(): bool
    {
        return !empty($this->explanation) || !empty($this->explanation_image);
    }

    /**
     * الحصول على رابط URL لصورة السؤال
     */
    public function getImageUrl()
    {
        if (!$this->question_image) {
            return null;
        }

        if (filter_var($this->question_image, FILTER_VALIDATE_URL)) {
            return $this->question_image;
        }

        return asset('storage/' . ltrim($this->question_image, '/'));
    }

    /**
     * الحصول على رابط URL لصورة الشرح
     */
    public function getExplanationImageUrl()
    {
        if (!$this->explanation_image) {
            return null;
        }

        if (filter_var($this->explanation_image, FILTER_VALIDATE_URL)) {
            return $this->explanation_image;
        }

        return asset('storage/' . ltrim($this->explanation_image, '/'));
    }

    /**
     * الحصول على ملخص السؤال
     */
    public function getSummary($length = 100): string
    {
        $text = strip_tags($this->question_text);
        if (strlen($text) <= $length) {
            return $text;
        }

        return substr($text, 0, $length) . '...';
    }

    /**
     * الحصول على Label الصعوبة بناء على القيمة المخزنة
     */
    public function getDifficultyLabel(): string
    {
        return match ($this->difficulty) {
            'easy' => 'سهل',
            'medium' => 'متوسط',
            'hard' => 'صعب',
            default => 'غير محدد'
        };
    }

    /**
     * الحصول على Label المحتوى
     */
    public function getContentLabel(): string
    {
        return match ($this->content) {
            'algebra' => 'Algebra',
            'advanced_math' => 'Advanced Math',
            'problem_solving_and_data_analysis' => 'Problem Solving and Data Analysis',
            'geometry_and_trigonometry' => 'Geometry and Trigonometry',
            default => 'غير محدد'
        };
    }

    /**
     * الحصول على إحصائيات هذا السؤال
     */
    public function getStatistics(): array
    {
        if (!$this->relationLoaded('studentAnswers')) {
            $this->load('studentAnswers');
        }

        $totalAnswers = $this->studentAnswers->count();
        $correctAnswers = $this->studentAnswers->where('is_correct', true)->count();

        return [
            'total_attempts' => $totalAnswers,
            'correct_attempts' => $correctAnswers,
            'incorrect_attempts' => $totalAnswers - $correctAnswers,
            'accuracy_rate' => $totalAnswers > 0 ? round(($correctAnswers / $totalAnswers) * 100, 2) : 0,
            'average_score' => $totalAnswers > 0 ? round($this->studentAnswers->avg('score_earned'), 2) : 0,
        ];
    }

    /**
     * التحقق مما إذا كان السؤال نشطًا
     */
    public function isActive(): bool
    {
        return !empty($this->question_text);
    }

    /**
     * الحصول على الوقت المقدر للإجابة
     */
    public function getEstimatedTime(): int
    {
        $baseTime = match ($this->type) {
            'mcq', 'mcd', 'multiple_choice' => 60,
            'tf', 'true_false' => 30,
            'numeric', 'number' => 45,
            'it', 'identification', 'text' => 90,
            default => 60
        };

        $multiplier = match ($this->difficulty) {
            'easy' => 1,
            'medium' => 1.5,
            'hard' => 2,
            default => 1
        };

        return (int) ($baseTime * $multiplier);
    }

    /**
     * إعادة ترتيب الأسئلة في الموديول
     */
    public static function reorderQuestions($testId, $moduleNumber): bool
    {
        $questions = self::where('test_id', $testId)
            ->withEffectiveModule($moduleNumber)
            ->orderBy('question_order')
            ->get();

        $order = 1;
        foreach ($questions as $question) {
            $question->question_order = $order++;
            $question->save();
        }

        return true;
    }

    /**
     * التحقق مما إذا كان هذا هو السؤال الأخير في الموديول
     */
    public function isLastInModule(): bool
    {
        if (!$this->test) {
            return false;
        }

        $maxOrder = $this->test->questions()
            ->withEffectiveModule($this->effective_module)
            ->max('question_order');

        return $this->question_order == $maxOrder;
    }

    /**
     * التحقق مما إذا كان هذا هو السؤال الأول في الموديول
     */
    public function isFirstInModule(): bool
    {
        if (!$this->test) {
            return false;
        }

        $minOrder = $this->test->questions()
            ->withEffectiveModule($this->effective_module)
            ->min('question_order');

        return $this->question_order == $minOrder;
    }
}
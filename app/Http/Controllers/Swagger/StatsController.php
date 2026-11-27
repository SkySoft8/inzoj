<?php

namespace App\Http\Controllers\Swagger;

use App\Http\Controllers\Controller;

/**
 * @OA\Tag(
 *     name="Stats",
 *     description="Progress statistics: weight and calories are free; measurements and photos require premium"
 * )
 *
 * @OA\Schema(
 *     schema="BodyLog",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="type", type="string", example="weight"),
 *     @OA\Property(property="value", type="number", example=75.5),
 *     @OA\Property(property="unit", type="string", example="kg"),
 *     @OA\Property(property="logged_at", type="string", format="date", example="2026-08-25"),
 *     @OA\Property(property="note", type="string", nullable=true)
 * )
 *
 * @OA\Schema(
 *     schema="ProgressPhoto",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="kind", type="string", enum={"before", "after", "progress"}),
 *     @OA\Property(property="taken_at", type="string", format="date"),
 *     @OA\Property(property="weight_at_time", type="number", nullable=true, example=75.5),
 *     @OA\Property(property="note", type="string", nullable=true),
 *     @OA\Property(property="url", type="string", example="/api/stats/photos/1/file")
 * )
 *
 * @OA\Schema(
 *     schema="StatsNutrient",
 *     type="object",
 *     @OA\Property(property="code", type="string", example="proteins"),
 *     @OA\Property(property="label", type="string", example="Белки"),
 *     @OA\Property(property="category", type="string", enum={"main", "mineral", "vitamin"}, example="main"),
 *     @OA\Property(property="eaten", type="number", nullable=true, description="Average per tracked day", example=95.4),
 *     @OA\Property(property="goal", type="number", nullable=true, example=120),
 *     @OA\Property(property="percent", type="integer", nullable=true, example=80),
 *     @OA\Property(property="unit", type="string", example="г"),
 *     @OA\Property(property="tracked", type="boolean", example=true),
 *     @OA\Property(property="estimated", type="boolean", description="True when nutrient coverage is below 95%", example=false),
 *     @OA\Property(property="coverage", type="number", description="Percent of eaten weight that had nutrient data", example=100)
 * )
 *
 * @OA\Schema(
 *     schema="StatsPeriodDay",
 *     type="object",
 *     @OA\Property(property="date", type="string", format="date", example="2026-10-01"),
 *     @OA\Property(property="value", type="number", example=1950),
 *     @OA\Property(property="goal", type="number", nullable=true, example=2200),
 *     @OA\Property(property="tracked", type="boolean", example=true)
 * )
 *
 * @OA\Schema(
 *     schema="StatsWeightPoint",
 *     type="object",
 *     @OA\Property(property="date", type="string", format="date", example="2026-10-01"),
 *     @OA\Property(property="value", type="number", example=75.5)
 * )
 *
 * @OA\Schema(
 *     schema="StatsRecommendation",
 *     type="object",
 *     @OA\Property(property="code", type="string", example="low_protein"),
 *     @OA\Property(property="title", type="string", example="Мало белка"),
 *     @OA\Property(property="text", type="string", example="Среднее потребление белка 80% от нормы"),
 *     @OA\Property(property="severity", type="string", enum={"info", "warning", "critical"}, example="warning"),
 *     @OA\Property(property="period", type="string", enum={"day", "week", "month", "period"}, example="week"),
 *     @OA\Property(property="metric", type="string", example="nutrient_percent"),
 *     @OA\Property(property="nutrient_code", type="string", nullable=true, example="proteins"),
 *     @OA\Property(property="value", type="number", nullable=true, example=80),
 *     @OA\Property(property="threshold", type="number", nullable=true, example=90),
 *     @OA\Property(property="threshold_max", type="number", nullable=true),
 *     @OA\Property(property="unit", type="string", nullable=true, example="%"),
 *     @OA\Property(property="priority", type="integer", example=10),
 *     @OA\Property(property="eaten", type="number", nullable=true, example=95.4),
 *     @OA\Property(property="goal", type="number", nullable=true, example=120),
 *     @OA\Property(property="percent", type="integer", nullable=true, example=80),
 *     @OA\Property(property="coverage", type="number", nullable=true, example=100)
 * )
 *
 * @OA\Schema(
 *     schema="StatsPeriod",
 *     type="object",
 *     @OA\Property(property="success", type="boolean", example=true),
 *     @OA\Property(
 *         property="period",
 *         type="object",
 *         @OA\Property(property="from", type="string", format="date", example="2026-10-01"),
 *         @OA\Property(property="to", type="string", format="date", example="2026-10-31"),
 *         @OA\Property(property="label", type="string", example="01.10–31.10"),
 *         @OA\Property(property="days", type="integer", example=31),
 *         @OA\Property(property="tracked_days", type="integer", example=20)
 *     ),
 *     @OA\Property(
 *         property="calories",
 *         type="object",
 *         @OA\Property(property="label", type="string", example="Калории"),
 *         @OA\Property(property="eaten", type="number", example=1850),
 *         @OA\Property(property="goal", type="number", nullable=true, example=2200),
 *         @OA\Property(property="percent", type="integer", nullable=true, example=84),
 *         @OA\Property(property="unit", type="string", example="ккал"),
 *         @OA\Property(property="days", type="array", @OA\Items(ref="#/components/schemas/StatsPeriodDay"))
 *     ),
 *     @OA\Property(property="bju", type="array", description="Proteins, fats and carbs macros", @OA\Items(ref="#/components/schemas/StatsNutrient")),
 *     @OA\Property(
 *         property="weight",
 *         type="object",
 *         @OA\Property(property="unit", type="string", example="кг"),
 *         @OA\Property(property="points", type="array", @OA\Items(ref="#/components/schemas/StatsWeightPoint")),
 *         @OA\Property(property="start", type="number", nullable=true, example=76.2),
 *         @OA\Property(property="current", type="number", nullable=true, example=75.5),
 *         @OA\Property(property="change", type="number", nullable=true, description="Null when fewer than two weight logs", example=-0.7)
 *     ),
 *     @OA\Property(
 *         property="water",
 *         type="object",
 *         @OA\Property(property="label", type="string", example="Вода"),
 *         @OA\Property(property="unit", type="string", example="л"),
 *         @OA\Property(property="eaten", type="number", example=1.8),
 *         @OA\Property(property="goal", type="number", nullable=true, example=2.5),
 *         @OA\Property(property="percent", type="integer", nullable=true, example=72),
 *         @OA\Property(property="tracked", type="boolean", example=true)
 *     ),
 *     @OA\Property(
 *         property="steps",
 *         type="object",
 *         @OA\Property(property="label", type="string", example="Шаги"),
 *         @OA\Property(property="unit", type="string", example="шаг."),
 *         @OA\Property(property="eaten", type="number", example=8500),
 *         @OA\Property(property="goal", type="number", nullable=true, example=10000),
 *         @OA\Property(property="percent", type="integer", nullable=true, example=85),
 *         @OA\Property(property="tracked", type="boolean", example=true)
 *     ),
 *     @OA\Property(
 *         property="nutrients",
 *         type="object",
 *         @OA\Property(property="coverage_percent", type="number", example=87.5),
 *         @OA\Property(property="has_data", type="boolean", example=true),
 *         @OA\Property(property="main", type="array", @OA\Items(ref="#/components/schemas/StatsNutrient")),
 *         @OA\Property(property="minerals", type="array", @OA\Items(ref="#/components/schemas/StatsNutrient")),
 *         @OA\Property(property="vitamins", type="array", @OA\Items(ref="#/components/schemas/StatsNutrient"))
 *     ),
 *     @OA\Property(property="recommendations", type="array", @OA\Items(ref="#/components/schemas/StatsRecommendation"))
 * )
 *
 * @OA\Schema(
 *     schema="StatsPeriodWeek",
 *     type="object",
 *     description="Same payload as StatsPeriod, but recommendations are returned under the legacy attention key",
 *     @OA\Property(property="success", type="boolean", example=true),
 *     @OA\Property(
 *         property="period",
 *         type="object",
 *         @OA\Property(property="from", type="string", format="date", example="2026-08-18"),
 *         @OA\Property(property="to", type="string", format="date", example="2026-08-24"),
 *         @OA\Property(property="label", type="string", example="18.08–24.08"),
 *         @OA\Property(property="days", type="integer", example=7),
 *         @OA\Property(property="tracked_days", type="integer", example=5)
 *     ),
 *     @OA\Property(
 *         property="calories",
 *         type="object",
 *         @OA\Property(property="label", type="string", example="Калории"),
 *         @OA\Property(property="eaten", type="number", example=1850),
 *         @OA\Property(property="goal", type="number", nullable=true, example=2200),
 *         @OA\Property(property="percent", type="integer", nullable=true, example=84),
 *         @OA\Property(property="unit", type="string", example="ккал"),
 *         @OA\Property(property="days", type="array", @OA\Items(ref="#/components/schemas/StatsPeriodDay"))
 *     ),
 *     @OA\Property(property="bju", type="array", description="Proteins, fats and carbs macros", @OA\Items(ref="#/components/schemas/StatsNutrient")),
 *     @OA\Property(
 *         property="weight",
 *         type="object",
 *         @OA\Property(property="unit", type="string", example="кг"),
 *         @OA\Property(property="points", type="array", @OA\Items(ref="#/components/schemas/StatsWeightPoint")),
 *         @OA\Property(property="start", type="number", nullable=true, example=76.2),
 *         @OA\Property(property="current", type="number", nullable=true, example=75.5),
 *         @OA\Property(property="change", type="number", nullable=true, description="Null when fewer than two weight logs", example=-0.7)
 *     ),
 *     @OA\Property(
 *         property="water",
 *         type="object",
 *         @OA\Property(property="label", type="string", example="Вода"),
 *         @OA\Property(property="unit", type="string", example="л"),
 *         @OA\Property(property="eaten", type="number", example=1.8),
 *         @OA\Property(property="goal", type="number", nullable=true, example=2.5),
 *         @OA\Property(property="percent", type="integer", nullable=true, example=72),
 *         @OA\Property(property="tracked", type="boolean", example=true)
 *     ),
 *     @OA\Property(
 *         property="steps",
 *         type="object",
 *         @OA\Property(property="label", type="string", example="Шаги"),
 *         @OA\Property(property="unit", type="string", example="шаг."),
 *         @OA\Property(property="eaten", type="number", example=8500),
 *         @OA\Property(property="goal", type="number", nullable=true, example=10000),
 *         @OA\Property(property="percent", type="integer", nullable=true, example=85),
 *         @OA\Property(property="tracked", type="boolean", example=true)
 *     ),
 *     @OA\Property(
 *         property="nutrients",
 *         type="object",
 *         @OA\Property(property="coverage_percent", type="number", example=87.5),
 *         @OA\Property(property="has_data", type="boolean", example=true),
 *         @OA\Property(property="main", type="array", @OA\Items(ref="#/components/schemas/StatsNutrient")),
 *         @OA\Property(property="minerals", type="array", @OA\Items(ref="#/components/schemas/StatsNutrient")),
 *         @OA\Property(property="vitamins", type="array", @OA\Items(ref="#/components/schemas/StatsNutrient"))
 *     ),
 *     @OA\Property(property="attention", type="array", @OA\Items(ref="#/components/schemas/StatsRecommendation"))
 * )
 *
 * @OA\Get(
 *     path="/api/stats/summary",
 *     summary="Progress summary",
 *     operationId="statsSummary",
 *     tags={"Stats"},
 *     security={{"userSanctumToken": {}}},
 *     @OA\Response(response=200, description="Start vs current weight, today calories; photos if premium")
 * )
 *
 * @OA\Get(
 *     path="/api/stats/period",
 *     summary="Statistics for any period",
 *     description="Returns calories, BJU, weight dynamics, main nutrients, minerals, vitamins and recommendations. Averages are calculated over tracked days. Micronutrient values come from product nutrient data; coverage_percent shows how much of the eaten weight had that data. Recommendations are loaded from the recommendations table and evaluated by metric, comparison, threshold and their own week/month/period window.",
 *     operationId="statsPeriod",
 *     tags={"Stats"},
 *     security={{"userSanctumToken": {}}},
 *     @OA\Parameter(name="from", in="query", @OA\Schema(type="string", format="date", example="2026-10-01")),
 *     @OA\Parameter(name="to", in="query", @OA\Schema(type="string", format="date", example="2026-10-31")),
 *     @OA\Parameter(name="period", in="query", description="Preset used when from/to are omitted", @OA\Schema(type="string", enum={"week", "month", "custom"}, default="week")),
 *     @OA\Response(
 *         response=200,
 *         description="Period statistics",
 *         @OA\JsonContent(ref="#/components/schemas/StatsPeriod")
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Invalid period or period longer than 366 days",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Period must be 366 days or less")
 *         )
 *     )
 * )
 *
 * @OA\Get(
 *     path="/api/stats/week",
 *     summary="Weekly statistics (alias of /api/stats/period)",
 *     description="Backward-compatible alias: same payload as /api/stats/period, defaults to the current week. Recommendations are returned under the legacy attention key.",
 *     operationId="statsWeek",
 *     tags={"Stats"},
 *     security={{"userSanctumToken": {}}},
 *     @OA\Parameter(name="from", in="query", @OA\Schema(type="string", format="date", example="2026-08-18")),
 *     @OA\Parameter(name="to", in="query", @OA\Schema(type="string", format="date", example="2026-08-24")),
 *     @OA\Response(
 *         response=200,
 *         description="Week layout data; recommendations are under the attention key",
 *         @OA\JsonContent(ref="#/components/schemas/StatsPeriodWeek")
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Invalid period",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="to must be on or after from")
 *         )
 *     )
 * )
 *
 * @OA\Get(
 *     path="/api/stats/series",
 *     summary="Chart series",
 *     description="Free metrics: weight, calories, burned_calories, proteins, fats, carbs, water, steps. Premium: neck, chest, waist, hips, biceps, glucose, blood_pressure_sys, blood_pressure_dia.",
 *     operationId="statsSeries",
 *     tags={"Stats"},
 *     security={{"userSanctumToken": {}}},
 *     @OA\Parameter(name="metric", in="query", required=true, @OA\Schema(type="string", example="calories")),
 *     @OA\Parameter(name="from", in="query", @OA\Schema(type="string", format="date", example="2026-06-01")),
 *     @OA\Parameter(name="to", in="query", @OA\Schema(type="string", format="date", example="2026-08-25")),
 *     @OA\Parameter(name="group", in="query", @OA\Schema(type="string", enum={"day", "week"}, default="day")),
 *     @OA\Response(
 *         response=200,
 *         description="Points for charts",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="metric", type="string", example="calories"),
 *             @OA\Property(
 *                 property="points",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="date", type="string", example="2026-08-24"),
 *                     @OA\Property(property="value", type="number", example=1800)
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(response=403, description="Premium required for this metric")
 * )
 *
 * @OA\Get(
 *     path="/api/stats/compare",
 *     summary="Before vs after",
 *     description="Weight is always returned. Photos are included only for premium users.",
 *     operationId="statsCompare",
 *     tags={"Stats"},
 *     security={{"userSanctumToken": {}}},
 *     @OA\Response(response=200, description="Start and current weight, optional photos")
 * )
 *
 * @OA\Get(
 *     path="/api/stats/logs",
 *     summary="List body logs",
 *     operationId="statsLogs",
 *     tags={"Stats"},
 *     security={{"userSanctumToken": {}}},
 *     @OA\Parameter(name="type", in="query", @OA\Schema(type="string", example="weight")),
 *     @OA\Parameter(name="from", in="query", @OA\Schema(type="string", format="date")),
 *     @OA\Parameter(name="to", in="query", @OA\Schema(type="string", format="date")),
 *     @OA\Response(response=200, description="Log list")
 * )
 *
 * @OA\Post(
 *     path="/api/stats/logs",
 *     summary="Create or update a log for a date",
 *     description="One value per type per day (upsert). Weight is free. Measurements, glucose and blood pressure require premium.",
 *     operationId="statsStoreLog",
 *     tags={"Stats"},
 *     security={{"userSanctumToken": {}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 required={"type", "value"},
 *                 @OA\Property(property="type", type="string", example="weight", description="weight, neck, chest, waist, hips, biceps, glucose, blood_pressure_sys, blood_pressure_dia"),
 *                 @OA\Property(property="value", type="number", example=75.5),
 *                 @OA\Property(property="logged_at", type="string", format="date", example="2026-08-25"),
 *                 @OA\Property(property="note", type="string", nullable=true)
 *             )
 *         )
 *     ),
 *     @OA\Response(response=201, description="Saved"),
 *     @OA\Response(response=403, description="Premium required")
 * )
 *
 * @OA\Delete(
 *     path="/api/stats/logs/{id}",
 *     summary="Delete a log",
 *     operationId="statsDeleteLog",
 *     tags={"Stats"},
 *     security={{"userSanctumToken": {}}},
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="Deleted")
 * )
 *
 * @OA\Get(
 *     path="/api/stats/photos",
 *     summary="List progress photos",
 *     operationId="statsPhotos",
 *     tags={"Stats"},
 *     security={{"userSanctumToken": {}}},
 *     @OA\Response(response=200, description="Photos"),
 *     @OA\Response(response=403, description="Premium required")
 * )
 *
 * @OA\Post(
 *     path="/api/stats/photos",
 *     summary="Upload a progress photo",
 *     operationId="statsStorePhoto",
 *     tags={"Stats"},
 *     security={{"userSanctumToken": {}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 required={"photo"},
 *                 @OA\Property(property="photo", type="string", format="binary"),
 *                 @OA\Property(property="kind", type="string", enum={"before", "after", "progress"}),
 *                 @OA\Property(property="taken_at", type="string", format="date"),
 *                 @OA\Property(property="weight", type="number", example=75.5),
 *                 @OA\Property(property="note", type="string")
 *             )
 *         )
 *     ),
 *     @OA\Response(response=201, description="Saved"),
 *     @OA\Response(response=403, description="Premium required")
 * )
 *
 * @OA\Get(
 *     path="/api/stats/photos/{id}/file",
 *     summary="Download own progress photo",
 *     description="Private file. Requires user Bearer token and premium. Frontend should fetch with Authorization, not a public img src.",
 *     operationId="statsShowPhotoFile",
 *     tags={"Stats"},
 *     security={{"userSanctumToken": {}}},
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="Image file"),
 *     @OA\Response(response=403, description="Premium required"),
 *     @OA\Response(response=404, description="Not found")
 * )
 *
 * @OA\Delete(
 *     path="/api/stats/photos/{id}",
 *     summary="Delete a progress photo",
 *     operationId="statsDeletePhoto",
 *     tags={"Stats"},
 *     security={{"userSanctumToken": {}}},
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="Deleted")
 * )
 */
class StatsController extends Controller
{
    //
}

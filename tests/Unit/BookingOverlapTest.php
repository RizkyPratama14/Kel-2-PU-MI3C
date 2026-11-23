<?php

namespace Tests\Unit;

use App\Services\BookingAvailabilityService;
use PHPUnit\Framework\TestCase;

/**
 * Task T3: Unit Test untuk logika overlap booking (tanpa database).
 *
 * Menggunakan pure unit test untuk memastikan formula overlap benar:
 *   mulai_A < selesai_B && selesai_A > mulai_B
 *
 * Khususnya memastikan jadwal berdampingan presisi TIDAK dianggap bentrok.
 */
class BookingOverlapTest extends TestCase
{
    /**
     * Test: Dua rentang yang tumpang-tindih sebagian harus BENTROK.
     *
     * Booking A: 10:00 - 14:00
     * Booking B: 12:00 - 16:00
     * → Overlap karena 10:00 < 16:00 && 14:00 > 12:00
     */
    public function test_overlapping_ranges_are_detected(): void
    {
        $result = BookingAvailabilityService::isOverlap(
            '2026-12-01 10:00:00', '2026-12-01 14:00:00',  // Booking A
            '2026-12-01 12:00:00', '2026-12-01 16:00:00'   // Booking B
        );

        $this->assertTrue($result, 'Rentang yang tumpang-tindih sebagian harus terdeteksi sebagai bentrok.');
    }

    /**
     * Test: Booking B sepenuhnya di dalam Booking A harus BENTROK.
     *
     * Booking A: 08:00 - 18:00
     * Booking B: 10:00 - 14:00
     * → Overlap
     */
    public function test_fully_contained_ranges_are_detected(): void
    {
        $result = BookingAvailabilityService::isOverlap(
            '2026-12-01 08:00:00', '2026-12-01 18:00:00',
            '2026-12-01 10:00:00', '2026-12-01 14:00:00'
        );

        $this->assertTrue($result, 'Booking yang sepenuhnya berada di dalam booking lain harus bentrok.');
    }

    /**
     * Test: Dua rentang identik harus BENTROK.
     *
     * Booking A: 10:00 - 14:00
     * Booking B: 10:00 - 14:00
     */
    public function test_identical_ranges_are_detected(): void
    {
        $result = BookingAvailabilityService::isOverlap(
            '2026-12-01 10:00:00', '2026-12-01 14:00:00',
            '2026-12-01 10:00:00', '2026-12-01 14:00:00'
        );

        $this->assertTrue($result, 'Dua rentang yang identik harus bentrok.');
    }

    /**
     * Test: Rentang yang benar-benar terpisah TIDAK bentrok.
     *
     * Booking A: 08:00 - 10:00
     * Booking B: 14:00 - 16:00
     */
    public function test_non_overlapping_ranges_are_not_detected(): void
    {
        $result = BookingAvailabilityService::isOverlap(
            '2026-12-01 08:00:00', '2026-12-01 10:00:00',
            '2026-12-01 14:00:00', '2026-12-01 16:00:00'
        );

        $this->assertFalse($result, 'Rentang yang benar-benar terpisah tidak boleh bentrok.');
    }

    /**
     * ✅ TEST KRITIS T3:
     * Jadwal berdampingan PRESISI (selesai 12:00, mulai 12:00) TIDAK BENTROK.
     *
     * Booking A: 08:00 - 12:00  (selesai tepat pukul 12:00)
     * Booking B: 12:00 - 16:00  (mulai tepat pukul 12:00)
     *
     * Karena kondisi overlap: mulai_A < selesai_B && selesai_A > mulai_B
     * → 08:00 < 16:00 (true) && 12:00 > 12:00 (FALSE!)
     * → Tidak overlap ✅
     */
    public function test_adjacent_ranges_with_exact_boundary_are_NOT_overlapping(): void
    {
        $result = BookingAvailabilityService::isOverlap(
            '2026-12-01 08:00:00', '2026-12-01 12:00:00',  // Booking A selesai 12:00
            '2026-12-01 12:00:00', '2026-12-01 16:00:00'   // Booking B mulai 12:00
        );

        $this->assertFalse(
            $result,
            'Jadwal berdampingan presisi (selesai=12:00 dan mulai=12:00) TIDAK boleh dianggap bentrok.'
        );
    }

    /**
     * ✅ Kebalikan: boundary berdampingan dari sisi lain juga TIDAK BENTROK.
     *
     * Booking A: 12:00 - 16:00
     * Booking B: 08:00 - 12:00
     */
    public function test_adjacent_ranges_reversed_are_NOT_overlapping(): void
    {
        $result = BookingAvailabilityService::isOverlap(
            '2026-12-01 12:00:00', '2026-12-01 16:00:00',
            '2026-12-01 08:00:00', '2026-12-01 12:00:00'
        );

        $this->assertFalse(
            $result,
            'Jadwal berdampingan presisi (terbalik) TIDAK boleh dianggap bentrok.'
        );
    }

    /**
     * Test: Rentang yang hanya overlap 1 menit harus BENTROK.
     *
     * Booking A: 08:00 - 12:01
     * Booking B: 12:00 - 16:00
     * → 08:00 < 16:00 (true) && 12:01 > 12:00 (true) → Overlap
     */
    public function test_one_minute_overlap_is_detected(): void
    {
        $result = BookingAvailabilityService::isOverlap(
            '2026-12-01 08:00:00', '2026-12-01 12:01:00',
            '2026-12-01 12:00:00', '2026-12-01 16:00:00'
        );

        $this->assertTrue($result, 'Overlap 1 menit harus terdeteksi sebagai bentrok.');
    }

    /**
     * Test: Rentang lintas hari (overnight) yang overlap.
     *
     * Booking A: 22:00 (hari 1) - 06:00 (hari 2)
     * Booking B: 04:00 (hari 2) - 10:00 (hari 2)
     */
    public function test_overnight_overlapping_ranges(): void
    {
        $result = BookingAvailabilityService::isOverlap(
            '2026-12-01 22:00:00', '2026-12-02 06:00:00',
            '2026-12-02 04:00:00', '2026-12-02 10:00:00'
        );

        $this->assertTrue($result, 'Booking overnight yang overlap harus terdeteksi.');
    }
}

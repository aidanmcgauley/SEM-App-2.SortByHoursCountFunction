<?php
declare(strict_types=1);
require('functions.inc.php');
use PHPUnit\Framework\TestCase;

final class FunctionsTest extends TestCase{

    # TESTING parameterChecker FUNCTION

    public function testValidInput(): void
    {
        $items = ['Lecture', 'Lab', 'Support', 'Canvas'];
        $attendances = [2, 1, 2, 2];
        $total_hours = [33, 22, 44, 55];

        // Call the parameterChecker function directly
        $output = parameterChecker($items, $attendances, $total_hours);

        // You can now assert the result without needing to decode JSON
        $this->assertFalse($output['error']);
        $this->assertEquals($items, $output['items']);
        $this->assertEquals($attendances, $output['attendance']);
        // Add more assertions as needed, for example:
        $this->assertEquals(getSortedAttendance($items, $attendances), $output['sorted_attendance']);
    }

    public function testEmptyItemName(): void
    {
        $items = ['Lecture', '', 'Support', 'Canvas'];
        $attendances = [2, 1, 2, 2];
        $total_hours = [33, 22, 44, 55];

        $output = parameterChecker($items, $attendances, $total_hours);

        $this->assertTrue($output['error']);
        $this->assertEquals("Item names cannot be empty.", $output['message']);
    }

    public function testAttendanceNotAnInteger(): void
    {
        $items = ['Lecture', 'Lab', 'Support', 'Canvas'];
        $attendances = [2, 'non-integer', 2, 2];
        $total_hours = [33, 22, 44, 55];

        $output = parameterChecker($items, $attendances, $total_hours);

        $this->assertTrue($output['error']);
        $this->assertEquals("Attendance hours must be integers.", $output['message']);
    }

    public function testTotalHoursNotAnInteger(): void
    {
        $items = ['Lecture', 'Lab', 'Support', 'Canvas'];
        $attendances = [2, 1, 2, 2];
        $total_hours = [33, 'non-integer', 44, 55];

        $output = parameterChecker($items, $attendances, $total_hours);

        $this->assertTrue($output['error']);
        $this->assertEquals("Total hours must be integers.", $output['message']);
    }

    public function testAttendanceExceedsTotalHours(): void
    {
        $items = ['Lecture', 'Lab', 'Support', 'Canvas'];
        $attendances = [2, 50, 2, 2];
        $total_hours = [33, 22, 44, 55];

        $output = parameterChecker($items, $attendances, $total_hours);

        $this->assertTrue($output['error']);
        $this->assertEquals("Attendance hours cannot exceed total assigned hours.", $output['message']);
    }

    public function testNegativeAttendance(): void
    {
        $items = ['Lecture', 'Lab', 'Support', 'Canvas'];
        $attendances = [2, -1, 2, 2];
        $total_hours = [33, 22, 44, 55];

        $output = parameterChecker($items, $attendances, $total_hours);

        $this->assertTrue($output['error']);
        $this->assertEquals("Attendance hours cannot be negative.", $output['message']);
    }

    public function testNegativeTotalHours(): void
    {
        $items = ['Lecture', 'Lab', 'Support', 'Canvas'];
        $attendances = [2, 1, 2, 2];
        $total_hours = [33, -22, 44, 55];

        $output = parameterChecker($items, $attendances, $total_hours);

        $this->assertTrue($output['error']);
        $this->assertEquals("Total hours cannot be negative.", $output['message']);
    }

    # TESTING getSortedAttendance 

    public function testNormalCase(): void
    {
        $items = ['Lecture', 'Lab', 'Support', 'Canvas'];
        $attendances = [2, 3, 4, 1];
        $expected = [
            ['item' => 'Support', 'attendance' => 4],
            ['item' => 'Lab', 'attendance' => 3],
            ['item' => 'Lecture', 'attendance' => 2],
            ['item' => 'Canvas', 'attendance' => 1],
        ];

        $result = getSortedAttendance($items, $attendances);
        $this->assertEquals($expected, $result);
    }

    public function testMixedPositiveAndNegativeAttendances(): void
    {
        $items = ['Lecture', 'Lab', 'Support', 'Canvas'];
        $attendances = [-2, 3, 4, -1];
        $expected = [
            ['item' => 'Support', 'attendance' => 4],
            ['item' => 'Lab', 'attendance' => 3],
            ['item' => 'Canvas', 'attendance' => -1],
            ['item' => 'Lecture', 'attendance' => -2],
        ];

        $result = getSortedAttendance($items, $attendances);
        $this->assertEquals($expected, $result);
    }

    public function testAllNegativeAttendances(): void
    {
        $items = ['Lecture', 'Lab', 'Support', 'Canvas'];
        $attendances = [-2, -3, -4, -1];
        $expected = [
            ['item' => 'Canvas', 'attendance' => -1],
            ['item' => 'Lecture', 'attendance' => -2],
            ['item' => 'Lab', 'attendance' => -3],
            ['item' => 'Support', 'attendance' => -4],
        ];

        $result = getSortedAttendance($items, $attendances);
        $this->assertEquals($expected, $result);
    }

    public function testLargeNumbers(): void
    {
        $items = ['Lecture', 'Lab', 'Support', 'Canvas'];
        $attendances = [1000000000, 3000000000, 2000000000, 4000000000];
        $expected = [
            ['item' => 'Canvas', 'attendance' => 4000000000],
            ['item' => 'Lab', 'attendance' => 3000000000],
            ['item' => 'Support', 'attendance' => 2000000000],
            ['item' => 'Lecture', 'attendance' => 1000000000],
        ];

        $result = getSortedAttendance($items, $attendances);
        $this->assertEquals($expected, $result);
    }

    public function testWithAllEqualValues(): void
    {
        $items = ['Lecture', 'Lab', 'Support', 'Canvas'];
        $attendances = [5, 5, 5, 5];
        $expected = [
            ['item' => 'Lecture', 'attendance' => 5],
            ['item' => 'Lab', 'attendance' => 5],
            ['item' => 'Support', 'attendance' => 5],
            ['item' => 'Canvas', 'attendance' => 5],
        ];

        $result = getSortedAttendance($items, $attendances);
        $this->assertEquals($expected, $result);
    }

}
?>
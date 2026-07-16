import java.util.ArrayList;
import java.util.List;

class CourseClass {
    int dayOfWeek;
    String start;
    String end;

    public CourseClass(int dayOfWeek, String start, String end) {
        this.dayOfWeek = dayOfWeek;
        this.start = start;
        this.end = end;
    }

    @Override
    public String toString() {
        return "{ dayOfWeek: " + dayOfWeek +
                ", start: \"" + start +
                "\", end: \"" + end + "\" }";
    }
}

public class CheckSchedule {
    public static int toMinutes(String time) {
        String[] parts = time.split(":");
        return Integer.parseInt(parts[0]) * 60
                + Integer.parseInt(parts[1]);
    }

    public static List<CourseClass> checkConflict(
            List<CourseClass> registeredClasses,
            CourseClass newClass) {

        List<CourseClass> conflicts = new ArrayList<>();

        int newStart = toMinutes(newClass.start);
        int newEnd = toMinutes(newClass.end);

        for (CourseClass c : registeredClasses) {

            if (c.dayOfWeek != newClass.dayOfWeek)
                continue;

            int oldStart = toMinutes(c.start);
            int oldEnd = toMinutes(c.end);

            // kiểm tra giao nhau
            if (newStart < oldEnd && oldStart < newEnd) {
                conflicts.add(c);
            }
        }
        return conflicts;
    }

    public static void main(String[] args) {

        List<CourseClass> registeredClasses = new ArrayList<>();

        registeredClasses.add(
                new CourseClass(2, "07:00", "09:00"));

        registeredClasses.add(
                new CourseClass(4, "13:00", "15:00"));

        CourseClass newClass =
                new CourseClass(2, "08:30", "10:30");

        List<CourseClass> result =
                checkConflict(registeredClasses, newClass);

        System.out.println("Có trùng: " + !result.isEmpty());

        System.out.println("Danh sách lớp trùng:");

        for (CourseClass c : result) {
            System.out.println(c);
        }
    }
}
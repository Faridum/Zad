import 'package:flutter/material.dart';

/// ------------------------------------------------------------
/// AppTheme
/// ------------------------------------------------------------
/// يحتوي على جميع الألوان - الخطوط - ثيمات الأزرار - ثيم الـ TextFields.
/// الهدف: أن تكون كل الصفحات متناسقة بصرياً بدون تكرار في الكود.
/// ------------------------------------------------------------
class AppTheme {
  // ----------------------------
  // Colors
  // ----------------------------
  static const Color primary = Color(0xFF055755); // الأخضر الأساسي
  static const Color gold = Color(0xFFE3C88D);    // المستخدم في الشعار
  static const Color textDark = Colors.black;
  static const Color textLight = Colors.white;
  static const Color inputFill = Color(0xFFF5F5F5); // خلفية الحقول
  static const Color greyLabel = Color(0xFF7A7A7A);

  // ----------------------------
  // Color Scheme
  // ----------------------------
  static const ColorScheme colorScheme = ColorScheme.light(
    primary: primary,
    secondary: primary,
    surface: Colors.white,
    onPrimary: Colors.white,
    onSecondary: Colors.white,
    onSurface: Colors.black,
  );

  // ----------------------------
  // Text Styles
  // ----------------------------
  static final TextTheme textTheme = TextTheme(
    displayLarge: const TextStyle(
      fontFamily: "Playfair",
      fontSize: 128,
      fontWeight: FontWeight.bold,
      color: gold,
      letterSpacing: 1,
    ),

    // نصوص عادية
    bodyMedium: const TextStyle(
      fontFamily: "SFPro",
      fontSize: 16,
      color: textDark,
    ),

    // نص زر
    labelLarge: const TextStyle(
      fontFamily: "SFPro",
      fontSize: 17,
      fontWeight: FontWeight.w600,
      color: Colors.white,
    ),
  );

  // ----------------------------
  // Logo Style (مثلاً للـ Welcome Page)
  // ----------------------------
  static const TextStyle logoText = TextStyle(
    fontFamily: "Playfair",
    fontSize: 128,
    fontWeight: FontWeight.bold,
    color: gold,
    letterSpacing: 1,
  );

  // ----------------------------
  // Input Decorations (للاستخدام العام)
  // ----------------------------
  static final OutlineInputBorder inputBorder = OutlineInputBorder(
    borderRadius: BorderRadius.circular(12),
    borderSide: BorderSide(color: Colors.grey.shade400),
  );

  static final OutlineInputBorder inputFocusedBorder = const OutlineInputBorder(
    borderRadius: BorderRadius.all(Radius.circular(12)),
    borderSide: BorderSide(color: primary, width: 1.4),
  );

  static const TextStyle inputHint = TextStyle(
    fontFamily: "SFPro",
    fontSize: 14,
    color: Color(0xFF999999),
  );

  static const TextStyle inputLabel = TextStyle(
    fontFamily: "SFPro",
    fontSize: 15,
    fontWeight: FontWeight.w500,
    color: textDark,
  );

  // ----------------------------
  // Button Text Style
  // ----------------------------
  static const TextStyle buttonText = TextStyle(
    fontFamily: "SFPro",
    fontSize: 16,
    fontWeight: FontWeight.w600,
    color: Colors.white,
  );

  // ----------------------------
  // THEME DATA — الأكثر أهمية
  // ----------------------------
  static ThemeData lightTheme = ThemeData(
    useMaterial3: true,
    fontFamily: "SFPro",
    colorScheme: colorScheme,
    textTheme: textTheme,

    // زر FilledButton
    filledButtonTheme: FilledButtonThemeData(
      style: FilledButton.styleFrom(
        backgroundColor: primary,
        foregroundColor: Colors.white,
        textStyle: buttonText,
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(16),
        ),
        minimumSize: const Size(double.infinity, 56),
      ),
    ),

    // ----------------------------
    // TextField Theme — مطابق للصورة بالضبط
    // ----------------------------
    inputDecorationTheme: InputDecorationTheme(
      filled: true,
      fillColor: inputFill,
      contentPadding: const EdgeInsets.symmetric(vertical: 16, horizontal: 14),

      hintStyle: inputHint,
      labelStyle: inputLabel,

      border: inputBorder,
      enabledBorder: inputBorder,
      focusedBorder: inputFocusedBorder,
    ),
  );
}

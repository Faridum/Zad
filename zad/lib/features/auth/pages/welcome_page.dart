import 'package:flutter/material.dart';
import 'package:flutter_animate/flutter_animate.dart';
import 'package:go_router/go_router.dart';
import 'package:grad_project/core/theme/app_theme.dart';

class WelcomePage extends StatelessWidget {
  const WelcomePage({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Stack(
        children: [
          // صورة الخلفية
          Positioned.fill(
            child: Image.asset(
              "assets/images/welcome_bg.jpg",
              fit: BoxFit.cover,
            ),
          ),

          // طبقة داكنة بسيطة
          Positioned.fill(
            child: Container(color: Colors.black.withOpacity(0.35)),
          ),

          // المحتوى
          SafeArea(
            child: Padding(
              padding: const EdgeInsets.symmetric(horizontal: 20),
              child: Column(
                children: [
                  const Spacer(flex: 3),

                  // الشعار + النص
                  const SizedBox(height: 200),
                  Column(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      Text(
                        "ZAD",
                        style: AppTheme.logoText,
                      )
                          .animate()
                          .fade(duration: 600.ms)
                          .moveY(begin: 20, end: 0, duration: 600.ms),


                      Text(
                        "Eat smart, save more, waste less.",
                        textAlign: TextAlign.center,
                        style: AppTheme.textTheme.bodyMedium!.copyWith(
                          color: Colors.white,
                          fontWeight: FontWeight.bold
                        ),
                      ),
                    ],
                  )
                      .animate(delay: 200.ms)
                      .fade(duration: 500.ms)
                      .moveY(begin: 10, end: 0, duration: 500.ms),

                  const Spacer(flex: 4),

                  // زر Sign in
                  SizedBox(
                    width: double.infinity,
                    child: FilledButton(
                      onPressed: () => context.push("/register"),
                      child: const Text("Sign in"),
                    ),
                  )
                      .animate()
                      .scaleXY(begin: 0.95, end: 1, duration: 400.ms)
                    .fade(duration: 400.ms),

                  const SizedBox(height: 14),

                  // زر Google
                  GestureDetector(
                    onTap: () {},
                    child: Container(
                      height: 56,
                      decoration: BoxDecoration(
                        color: AppTheme.textDark,
                        borderRadius: BorderRadius.circular(16),
                      ),
                      child: Row(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          Image.asset(
                            "assets/icons/google_logo.jpg",
                            width: 24,
                            height: 24,
                          ),
                          const SizedBox(width: 12),
                          Text(
                            "Continue with Google",
                            style: AppTheme.textTheme.bodyMedium!.copyWith(
                              color: Colors.white,
                              fontWeight: FontWeight.bold
                            ),
                          ),
                        ],
                      ),
                    ),
                  )
                  ///animation
                      .animate(delay: 150.ms)
                      .scaleXY(begin: 0.95, end: 1, duration: 400.ms)
                      .fade(duration: 400.ms),

                  const SizedBox(height: 18),

                  GestureDetector(
                    onTap: () => context.push("/login"),
                    child: RichText(
                      text: TextSpan(
                        style: AppTheme.textTheme.bodyMedium!.copyWith(
                          fontSize: 14,
                        ),
                        children: [
                          const TextSpan(text: "Already have an account? "),
                          TextSpan(
                            text: "Login",
                            style: const TextStyle(
                              decoration: TextDecoration.underline,
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                        ],
                      ),
                    ),
                  )
                .animate()
                .fade(duration: 500.ms),

                  const SizedBox(height: 28),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }
}

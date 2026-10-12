// lib/features/auth/pages/login_page.dart

import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../../core/theme/app_theme.dart';
import '../providers/auth_provider.dart';

class LoginPage extends ConsumerStatefulWidget {
  const LoginPage({super.key});

  @override
  ConsumerState<LoginPage> createState() => _LoginPageState();
}

class _LoginPageState extends ConsumerState<LoginPage> {
  final _formKey = GlobalKey<FormState>();

  final emailController = TextEditingController();
  final passwordController = TextEditingController();

  @override
  void dispose() {
    emailController.dispose();
    passwordController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final auth = ref.watch(authProvider);
    final textTheme = Theme.of(context).textTheme;

    return Scaffold(
      backgroundColor: Colors.white,
      body: SafeArea(
        child: Padding(
          padding: const EdgeInsets.symmetric(horizontal: 24),
          child: Form(
            key: _formKey,
            child: SingleChildScrollView(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const SizedBox(height: 20),

                  // LOGO
                  Center(
                    child: Text(
                      "ZAD",
                      style: AppTheme.logoText.copyWith(
                        fontSize: 64,
                        color: AppTheme.primary,
                      ),
                    ),
                  ),

                  const SizedBox(height: 32),

                  // TITLE
                  Text(
                    "Login",
                    style: textTheme.bodyMedium?.copyWith(
                      fontSize: 32,
                      fontWeight: FontWeight.bold,
                    ),
                  ),

                  const SizedBox(height: 6),

                  // SUBTITLE
                  Text(
                    "Welcome back, Glad to see you again!",
                    style: textTheme.bodyMedium?.copyWith(
                      fontSize: 15,
                      color: AppTheme.textDark.withOpacity(0.7),
                    ),
                  ),

                  const SizedBox(height: 32),

                  // EMAIL
                  const Text("Email Address", style: AppTheme.inputLabel),
                  const SizedBox(height: 8),

                  TextFormField(
                    controller: emailController,
                    keyboardType: TextInputType.emailAddress,
                    textInputAction: TextInputAction.next,
                    decoration: const InputDecoration(
                      hintText: "Enter your email address",
                    ),
                    validator: (v) =>
                    v == null || v.trim().isEmpty
                        ? "Email is required"
                        : null,
                  ),

                  const SizedBox(height: 20),

                  // PASSWORD
                  const Text("Password", style: AppTheme.inputLabel),
                  const SizedBox(height: 8),

                  TextFormField(
                    controller: passwordController,
                    obscureText: true,
                    textInputAction: TextInputAction.done,
                    decoration: const InputDecoration(
                      hintText: "Password",
                    ),
                    validator: (v) =>
                    v == null || v.trim().isEmpty
                        ? "Password is required"
                        : null,
                  ),

                  const SizedBox(height: 16),

                  // FORGOT PASSWORD
                  Align(
                    alignment: Alignment.centerRight,
                    child: GestureDetector(
                      onTap: () {},
                      child: Text(
                        "Forgot Password?",
                        style: textTheme.bodyMedium?.copyWith(
                          fontSize: 14,
                          color: AppTheme.primary,
                        ),
                      ),
                    ),
                  ),

                  const SizedBox(height: 30),

                  // LOGIN BUTTON
                  SizedBox(
                    width: double.infinity,
                    height: 54,
                    child: FilledButton(
                      onPressed: auth.isLoading
                          ? null
                          : () async {
                        FocusScope.of(context).unfocus();

                        if (!_formKey.currentState!.validate()) {
                          return;
                        }

                        await ref
                            .read(authProvider.notifier)
                            .login(
                          emailController.text.trim(),
                          passwordController.text.trim(),
                        );

                        if (!mounted) return;

                        if (ref.read(authProvider).isLoggedIn) {
                          context.go("/home");
                        }
                      },
                      child: auth.isLoading
                          ? const CircularProgressIndicator(
                        color: Colors.white,
                      )
                          : const Text("Log in"),
                    ),
                  ),

                  // ERROR MESSAGE
                  if (auth.error != null) ...[
                    const SizedBox(height: 16),
                    Text(
                      auth.error!,
                      style: const TextStyle(color: Colors.red),
                    ),
                  ],

                  const SizedBox(height: 24),

                  // REGISTER LINK
                  Center(
                    child: GestureDetector(
                      onTap: () => context.go("/register"),
                      child: RichText(
                        text: TextSpan(
                          style:
                          textTheme.bodyMedium?.copyWith(fontSize: 14),
                          children: const [
                            TextSpan(text: "Don’t have an account? "),
                            TextSpan(
                              text: "Sign up",
                              style: TextStyle(
                                color: AppTheme.primary,
                                fontWeight: FontWeight.w600,
                              ),
                            ),
                          ],
                        ),
                      ),
                    ),
                  ),

                  const SizedBox(height: 30),
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }
}

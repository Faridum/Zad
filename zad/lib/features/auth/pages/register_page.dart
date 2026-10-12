// lib/features/auth/pages/register_page.dart

import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../../core/theme/app_theme.dart';
import '../providers/auth_provider.dart';

class RegisterPage extends ConsumerStatefulWidget {
  const RegisterPage({super.key});

  @override
  ConsumerState<RegisterPage> createState() => _RegisterPageState();
}

class _RegisterPageState extends ConsumerState<RegisterPage> {
  final _formKey = GlobalKey<FormState>();

  final nameCtrl = TextEditingController();
  final emailCtrl = TextEditingController();
  final phoneCtrl = TextEditingController();
  final passCtrl = TextEditingController();
  final confirmCtrl = TextEditingController();

  @override
  void dispose() {
    nameCtrl.dispose();
    emailCtrl.dispose();
    phoneCtrl.dispose();
    passCtrl.dispose();
    confirmCtrl.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final auth = ref.watch(authProvider);

    return Scaffold(
      backgroundColor: Colors.white,
      body: SafeArea(
        child: Padding(
          padding: const EdgeInsets.symmetric(horizontal: 28),
          child: Form(
            key: _formKey,
            child: ListView(
              children: [
                const SizedBox(height: 40),

                Center(
                  child: Text(
                    "ZAD",
                    style: AppTheme.logoText.copyWith(
                      fontSize: 40,
                      color: AppTheme.gold,
                    ),
                  ),
                ),

                const SizedBox(height: 32),

                const Text(
                  "Create Account",
                  style: TextStyle(
                    fontSize: 28,
                    fontWeight: FontWeight.bold,
                  ),
                ),

                const SizedBox(height: 6),

                Text(
                  "Create your account to continue",
                  style: TextStyle(color: AppTheme.greyLabel),
                ),

                const SizedBox(height: 28),

                _input(
                  controller: nameCtrl,
                  hint: "Full Name",
                  validator: (v) =>
                  v == null || v.trim().isEmpty ? "Name is required" : null,
                ),

                const SizedBox(height: 14),

                _input(
                  controller: emailCtrl,
                  hint: "Email",
                  keyboard: TextInputType.emailAddress,
                  validator: (v) {
                    if (v == null || v.trim().isEmpty) {
                      return "Email is required";
                    }
                    if (!v.contains("@")) {
                      return "Invalid email";
                    }
                    return null;
                  },
                ),

                const SizedBox(height: 14),

                _input(
                  controller: phoneCtrl,
                  hint: "Phone Number",
                  keyboard: TextInputType.phone,
                  validator: (v) {
                    if (v == null || v.trim().isEmpty) {
                      return "Phone number is required";
                    }
                    if (!RegExp(r'^[0-9]+$').hasMatch(v)) {
                      return "Phone must contain digits only";
                    }
                    if (v.length < 8) {
                      return "Invalid phone number";
                    }
                    return null;
                  },
                ),

                const SizedBox(height: 14),

                _input(
                  controller: passCtrl,
                  hint: "Password",
                  obscure: true,
                  validator: (v) {
                    if (v == null || v.isEmpty) {
                      return "Password is required";
                    }
                    if (v != passCtrl.text) {
                      return "Passwords do not match";
                    }
                    return null;
                  },
                ),

                const SizedBox(height: 14),

                _input(
                  controller: confirmCtrl,
                  hint: "Confirm Password",
                  obscure: true,
                  validator: (v) {
                    if (v == null || v.isEmpty) {
                      return "Confirm your password";
                    }
                    if (v != passCtrl.text) {
                      return "Passwords do not match";
                    }
                    return null;
                  },
                ),

                const SizedBox(height: 24),

                FilledButton(
                  onPressed: auth.isLoading
                      ? null
                      : () async {
                    FocusScope.of(context).unfocus();

                    if (!_formKey.currentState!.validate()) return;

                    await ref.read(authProvider.notifier).register(
                      nameCtrl.text.trim(),
                      emailCtrl.text.trim(),
                      passCtrl.text.trim(),
                      confirmCtrl.text.trim(),
                      phoneCtrl.text.trim(),
                    );

                    if (!mounted) return;

                    if (ref.read(authProvider).error == null) {
                      context.go("/login");
                    }
                  },
                  child: auth.isLoading
                      ? const CircularProgressIndicator(color: Colors.white)
                      : const Text("Create Account"),
                ),

                if (auth.error != null) ...[
                  const SizedBox(height: 12),
                  Text(
                    auth.error!,
                    style: const TextStyle(color: Colors.red),
                  ),
                ],

                const SizedBox(height: 20),

                Center(
                  child: GestureDetector(
                    onTap: () => context.go("/login"),
                    child: const Text(
                      "Already have an account? Login",
                      style: TextStyle(
                        color: AppTheme.primary,
                        fontWeight: FontWeight.w600,
                      ),
                    ),
                  ),
                ),

                const SizedBox(height: 40),
              ],
            ),
          ),
        ),
      ),
    );
  }

  Widget _input({
    required TextEditingController controller,
    required String hint,
    bool obscure = false,
    TextInputType keyboard = TextInputType.text,
    String? Function(String?)? validator,
  }) {
    return TextFormField(
      controller: controller,
      obscureText: obscure,
      keyboardType: keyboard,
      validator: validator,
      decoration: InputDecoration(hintText: hint),
    );
  }
}

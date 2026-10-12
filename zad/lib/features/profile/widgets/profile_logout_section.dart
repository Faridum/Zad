import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';


import '../../auth/providers/logout_provider.dart';
import 'profile_menu_item.dart';


class ProfileLogoutSection extends ConsumerWidget {
  const ProfileLogoutSection({super.key});


  @override
  Widget build(BuildContext context, WidgetRef ref) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const SizedBox(height: 12),
        Container(
          padding: const EdgeInsets.symmetric(horizontal: 16),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(16),
            boxShadow: [
              BoxShadow(
                color: Colors.black.withOpacity(0.05),
                blurRadius: 10,
                offset: const Offset(0, 4),
              ),
            ],
          ),
          child: ProfileMenuItem(
            icon: Icons.logout,
            title: 'Logout',
            onTap: () async {
              // ✅ 1. استدعاء دالة الـ logout
              final logout = ref.read(logoutProvider);
              await logout();


              // ✅ 2. أمان
              if (!context.mounted) return;


              // ✅ 3. خروج كامل من التطبيق
              context.go('/welcome');
            },
          ),
        ),
      ],
    );
  }
}


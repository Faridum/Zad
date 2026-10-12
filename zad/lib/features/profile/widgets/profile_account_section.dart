import 'package:flutter/material.dart';
import '../pages/manage_profile_page.dart';
import '../pages/password_security_page.dart';
import 'profile_menu_item.dart';

class ProfileAccountSection extends StatelessWidget {
  const ProfileAccountSection({super.key, required Map<String, dynamic> user});

  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const Text(
          'Account',
          style: TextStyle(
            fontSize: 14,
            color: Colors.grey,
            fontWeight: FontWeight.w500,
          ),
        ),
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
          child: Column(
            children:  [
              ProfileMenuItem(
                icon: Icons.person_outline,
                title: 'Manage Profile',
                onTap: () {
                  Navigator.push(
                    context,
                    MaterialPageRoute(
                      builder: (_) => const ManageProfilePage(),
                    ),
                  );
                },
              ),

              ProfileMenuItem(
                icon: Icons.lock_outline,
                title: "Password & Security",
                onTap: () {
                  Navigator.push(
                    context,
                    MaterialPageRoute(
                      builder: (_) => const PasswordSecurityPage(),
                    ),
                  );
                },
              ),

              ProfileMenuItem(
                icon: Icons.language,
                title: 'Language',
                trailingText: 'English',
              ),
            ],
          ),
        ),
      ],
    );
  }
}


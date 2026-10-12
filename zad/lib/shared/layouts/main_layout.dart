// shared/layouts/main_layout.dart
import 'package:flutter/material.dart';
import '../widgets/app_bottom_nav.dart';

class MainLayout extends StatelessWidget {
  final Widget child;
  final Widget? drawer;

  const MainLayout({
    super.key,
    required this.child,
    this.drawer,
  });

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      drawer: drawer,
      body: child,
      bottomNavigationBar: const AppBottomNav(),
    );
  }
}


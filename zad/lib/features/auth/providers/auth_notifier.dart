// lib/features/auth/providers/auth_notifier.dart
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:grad_project/features/auth/providers/user_profile_provider.dart';
import 'auth_state.dart';
import '../../../shared/services/api_service.dart';
import '../../../core/storage/local_storage.dart';

class AuthNotifier extends StateNotifier<AuthState> {
  final Ref ref;

  AuthNotifier(this.ref) : super(AuthState.initial());

  Future<void> login(String email, String password) async {
    state = state.copyWith(isLoading: true, error: null);

    final response = await ApiService.post(
      "auth/login.php",
      body: {"email": email, "password": password},
    );

    if (response.success) {
      final token = response.data["token"];

      await LocalStorage.saveToken(token);

      // ⭐ هذا هو السطر المهم
      ref.invalidate(userProfileProvider);

      state = state.copyWith(
        isLoading: false,
        isLoggedIn: true,
      );
    } else {
      state = state.copyWith(isLoading: false, error: response.message);
    }
  }

  // ✅ هذه هي الدالة التي يشتكي منها Flutter
  Future<void> register(
      String name,
      String email,
      String password,
      String confirmPassword,
      String phone,
      ) async {
    state = state.copyWith(isLoading: true, error: null);

    final response = await ApiService.post(
      "auth/register.php",
      body: {
        "name": name,
        "email": email,
        "password": password,
        "passwordConfirmation": confirmPassword,
        "phone": phone,
      },
    );

    if (response.success) {
      state = state.copyWith(isLoading: false);
    } else {
      state = state.copyWith(isLoading: false, error: response.message);
    }
  }

  Future<void> logout() async {
    await LocalStorage.clear();
    state = AuthState.initial();
  }
}





import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../core/storage/local_storage.dart';
import '../providers/user_profile_provider.dart';
import '../../home/providers/home_provider.dart';


final logoutProvider = Provider<Future<void> Function()>((ref) {
  return () async {
    // 1️⃣ حذف التوكن
    await LocalStorage.clear();


    // 2️⃣ إعادة تعيين providers المرتبطة بالمستخدم
    ref.invalidate(userProfileProvider);
    ref.invalidate(homeProvider);
  };
});

